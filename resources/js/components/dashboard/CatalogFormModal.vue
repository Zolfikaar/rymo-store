<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { CategoryFormData, NameSlugFormData } from '@/types/catalog';
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

type FormMode = 'create' | 'edit';

const props = withDefaults(
    defineProps<{
        open: boolean;
        mode: FormMode;
        resourceType: 'category' | 'brand';
        item?: {
            id: number;
            name: string;
            slug: string;
            description?: string | null;
            is_active?: boolean;
        } | null;
    }>(),
    {
        item: null,
    },
);

const emit = defineEmits<{
    (event: 'update:open', value: boolean): void;
}>();

const isCategory = computed(() => props.resourceType === 'category');

const form = useForm<CategoryFormData | NameSlugFormData>(
    isCategory.value
        ? {
              name: '',
              slug: '',
              description: '',
              is_active: true,
          }
        : {
              name: '',
              slug: '',
          },
);

const title = computed(() => {
    const label = isCategory.value ? 'Category' : 'Brand';
    return props.mode === 'create' ? `Add ${label}` : `Edit ${label}`;
});

const submitRoute = computed(() => {
    if (isCategory.value) {
        return props.mode === 'create'
            ? route('dashboard.categories.store')
            : route('dashboard.categories.update', props.item!.id);
    }

    return props.mode === 'create'
        ? route('dashboard.brands.store')
        : route('dashboard.brands.update', props.item!.id);
});

function resetForm(): void {
    if (isCategory.value) {
        form.defaults({
            name: props.item?.name ?? '',
            slug: props.item?.slug ?? '',
            description: props.item?.description ?? '',
            is_active: props.item?.is_active ?? true,
        });
    } else {
        form.defaults({
            name: props.item?.name ?? '',
            slug: props.item?.slug ?? '',
        });
    }

    form.reset();
    form.clearErrors();
}

watch(
    () => [props.open, props.mode, props.item],
    () => {
        if (props.open) {
            resetForm();
        }
    },
    { immediate: true },
);

function closeModal(): void {
    emit('update:open', false);
}

function submit(): void {
    const options = {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    };

    if (props.mode === 'create') {
        form.post(submitRoute.value, options);
        return;
    }

    form.put(submitRoute.value, options);
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="dashboard-form-modal sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ title }}</DialogTitle>
                <DialogDescription class="dashboard-form-modal__description">
                    {{ mode === 'create' ? 'Create a new catalog entry.' : 'Update the selected entry.' }}
                </DialogDescription>
            </DialogHeader>

            <form class="space-y-4" @submit.prevent="submit">
                <div class="space-y-2">
                    <Label for="catalog-name">Name</Label>
                    <Input id="catalog-name" v-model="form.name" required autocomplete="off" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="space-y-2">
                    <Label for="catalog-slug">Slug</Label>
                    <Input id="catalog-slug" v-model="form.slug" autocomplete="off" placeholder="auto-generated if empty" />
                    <InputError :message="form.errors.slug" />
                </div>

                <template v-if="isCategory">
                    <div class="space-y-2">
                        <Label for="catalog-description">Description</Label>
                        <textarea
                            id="catalog-description"
                            v-model="(form as CategoryFormData).description"
                            rows="3"
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        />
                        <InputError :message="form.errors.description" />
                    </div>

                    <label class="dashboard-form-modal__checkbox-label">
                        <input v-model="(form as CategoryFormData).is_active" type="checkbox" class="rounded border-input" />
                        Active category
                    </label>
                </template>

                <InputError :message="form.errors.category ?? form.errors.brand" />

                <DialogFooter class="gap-2 sm:gap-0">
                    <Button type="button" variant="outline" class="dashboard-modal-btn--outline" @click="closeModal">
                        Cancel
                    </Button>
                    <Button type="submit" class="dashboard-modal-btn--primary" :disabled="form.processing">
                        {{ form.processing ? 'Saving…' : mode === 'create' ? 'Create' : 'Save changes' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
