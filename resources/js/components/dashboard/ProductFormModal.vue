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
import {
    CLOTHING_SIZE_OPTIONS,
    SHOE_SIZE_OPTIONS,
    type CatalogBrand,
    type CatalogCategory,
    type CatalogProduct,
    type ProductFormData,
} from '@/types/catalog';
import { useForm } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, ref, watch } from 'vue';

type FormMode = 'create' | 'edit';

const props = withDefaults(
    defineProps<{
        open: boolean;
        mode: FormMode;
        product?: CatalogProduct | null;
        categories: CatalogCategory[];
        brands: CatalogBrand[];
        sizeOptions: string[];
    }>(),
    {
        product: null,
    },
);

const emit = defineEmits<{
    (event: 'update:open', value: boolean): void;
}>();

const form = useForm<ProductFormData>({
    name: '',
    slug: '',
    description: '',
    price: '',
    stock: '0',
    sku: '',
    color: '',
    category_id: '',
    brand_id: '',
    image: null,
    remove_image: false,
    existing_gallery_images: [],
    gallery_uploads: [],
    available_sizes: [],
});

const mainImageInput = ref<HTMLInputElement | null>(null);
const galleryInput = ref<HTMLInputElement | null>(null);
const mainImagePreview = ref<string | null>(null);
const newGalleryPreviews = ref<string[]>([]);
const currentMainImageUrl = ref<string | null>(null);

const title = computed(() => (props.mode === 'create' ? 'Add Product' : 'Edit Product'));

const displayedMainImage = computed(() => mainImagePreview.value ?? currentMainImageUrl.value);

const clothingSizes = computed(() =>
    props.sizeOptions.filter((size) => (CLOTHING_SIZE_OPTIONS as readonly string[]).includes(size)),
);

const shoeSizes = computed(() =>
    props.sizeOptions.filter((size) => (SHOE_SIZE_OPTIONS as readonly string[]).includes(size)),
);

function revokePreviewUrls(): void {
    if (mainImagePreview.value) {
        URL.revokeObjectURL(mainImagePreview.value);
        mainImagePreview.value = null;
    }

    newGalleryPreviews.value.forEach((previewUrl) => URL.revokeObjectURL(previewUrl));
    newGalleryPreviews.value = [];
}

function resetForm(): void {
    revokePreviewUrls();

    currentMainImageUrl.value = props.product?.image_url ?? null;

    form.defaults({
        name: props.product?.name ?? '',
        slug: props.product?.slug ?? '',
        description: props.product?.description ?? '',
        price: props.product?.price ?? '',
        stock: String(props.product?.stock ?? 0),
        sku: props.product?.sku ?? '',
        color: props.product?.color ?? '',
        category_id: props.product?.category_id ? String(props.product.category_id) : '',
        brand_id: props.product?.brand_id ? String(props.product.brand_id) : '',
        image: null,
        remove_image: false,
        existing_gallery_images: [...(props.product?.gallery_images ?? [])],
        gallery_uploads: [],
        available_sizes: [...(props.product?.available_sizes ?? [])],
    });
    form.reset();
    form.clearErrors();

    if (mainImageInput.value) {
        mainImageInput.value.value = '';
    }

    if (galleryInput.value) {
        galleryInput.value.value = '';
    }
}

watch(
    () => [props.open, props.mode, props.product],
    () => {
        if (props.open) {
            resetForm();
        }
    },
    { immediate: true },
);

onBeforeUnmount(() => {
    revokePreviewUrls();
});

function closeModal(): void {
    emit('update:open', false);
}

function toggleSize(size: string): void {
    if (form.available_sizes.includes(size)) {
        form.available_sizes = form.available_sizes.filter((value) => value !== size);
        return;
    }

    form.available_sizes = [...form.available_sizes, size];
}

function openMainImagePicker(): void {
    mainImageInput.value?.click();
}

function onMainImageSelected(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;

    if (mainImagePreview.value) {
        URL.revokeObjectURL(mainImagePreview.value);
        mainImagePreview.value = null;
    }

    form.image = file;
    form.remove_image = false;
    mainImagePreview.value = file ? URL.createObjectURL(file) : null;
}

function clearMainImageSelection(): void {
    if (mainImagePreview.value) {
        URL.revokeObjectURL(mainImagePreview.value);
        mainImagePreview.value = null;
    }

    form.image = null;
    form.remove_image = true;
    currentMainImageUrl.value = null;

    if (mainImageInput.value) {
        mainImageInput.value.value = '';
    }
}

function openGalleryPicker(): void {
    galleryInput.value?.click();
}

function onGalleryFilesSelected(event: Event): void {
    const files = Array.from((event.target as HTMLInputElement).files ?? []);

    files.forEach((file) => {
        form.gallery_uploads = [...form.gallery_uploads, file];
        newGalleryPreviews.value = [...newGalleryPreviews.value, URL.createObjectURL(file)];
    });

    (event.target as HTMLInputElement).value = '';
}

function removeExistingGalleryImage(index: number): void {
    form.existing_gallery_images = form.existing_gallery_images.filter((_, itemIndex) => itemIndex !== index);
}

function removeNewGalleryImage(index: number): void {
    const previewUrl = newGalleryPreviews.value[index];

    if (previewUrl) {
        URL.revokeObjectURL(previewUrl);
    }

    newGalleryPreviews.value = newGalleryPreviews.value.filter((_, itemIndex) => itemIndex !== index);
    form.gallery_uploads = form.gallery_uploads.filter((_, itemIndex) => itemIndex !== index);
}

function submit(): void {
    const isUpdate = props.mode === 'edit';

    form.transform((data) => ({
        ...data,
        category_id: data.category_id === '' ? null : Number(data.category_id),
        brand_id: data.brand_id === '' ? null : Number(data.brand_id),
        ...(isUpdate ? { _method: 'put' } : {}),
    }));

    const options = {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => closeModal(),
        onFinish: () => form.transform((data) => data),
    };

    if (props.mode === 'create') {
        form.post(route('dashboard.products.store'), options);
        return;
    }

    form.post(route('dashboard.products.update', props.product!.id), options);
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="dashboard-form-modal max-h-[90vh] overflow-y-auto sm:max-w-2xl">
            <DialogHeader>
                <DialogTitle>{{ title }}</DialogTitle>
                <DialogDescription class="dashboard-form-modal__description">
                    Upload photos from your device and manage product inventory details.
                </DialogDescription>
            </DialogHeader>

            <form class="space-y-5" @submit.prevent="submit">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2 sm:col-span-2">
                        <Label for="product-name">Name</Label>
                        <Input id="product-name" v-model="form.name" required />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="space-y-2">
                        <Label for="product-slug">Slug</Label>
                        <Input id="product-slug" v-model="form.slug" placeholder="auto-generated if empty" />
                        <InputError :message="form.errors.slug" />
                    </div>

                    <div class="space-y-2">
                        <Label for="product-sku">SKU</Label>
                        <Input id="product-sku" v-model="form.sku" required />
                        <InputError :message="form.errors.sku" />
                    </div>

                    <div class="space-y-2">
                        <Label for="product-price">Price</Label>
                        <Input id="product-price" v-model="form.price" type="number" min="0" step="0.01" required />
                        <InputError :message="form.errors.price" />
                    </div>

                    <div class="space-y-2">
                        <Label for="product-stock">Stock</Label>
                        <Input id="product-stock" v-model="form.stock" type="number" min="0" step="1" required />
                        <InputError :message="form.errors.stock" />
                    </div>

                    <div class="space-y-2">
                        <Label for="product-color">Color</Label>
                        <Input id="product-color" v-model="form.color" />
                        <InputError :message="form.errors.color" />
                    </div>

                    <div class="space-y-2">
                        <Label for="product-category">Category</Label>
                        <select
                            id="product-category"
                            v-model="form.category_id"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="">None</option>
                            <option v-for="category in categories" :key="category.id" :value="String(category.id)">
                                {{ category.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.category_id" />
                    </div>

                    <div class="space-y-2">
                        <Label for="product-brand">Brand</Label>
                        <select
                            id="product-brand"
                            v-model="form.brand_id"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        >
                            <option value="">None</option>
                            <option v-for="brand in brands" :key="brand.id" :value="String(brand.id)">
                                {{ brand.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.brand_id" />
                    </div>

                    <div class="space-y-2 sm:col-span-2">
                        <Label for="product-description">Description</Label>
                        <textarea
                            id="product-description"
                            v-model="form.description"
                            rows="4"
                            class="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        />
                        <InputError :message="form.errors.description" />
                    </div>
                </div>

                <div class="space-y-3">
                    <Label>Main Product Image</Label>
                    <div class="flex flex-wrap items-center gap-3">
                        <img
                            v-if="displayedMainImage"
                            :src="displayedMainImage"
                            alt="Product preview"
                            class="dashboard-image-preview"
                        />
                        <div class="flex flex-wrap gap-2">
                            <label class="dashboard-image-upload" @click.prevent="openMainImagePicker">
                                {{ displayedMainImage ? 'Replace image' : 'Choose image' }}
                            </label>
                            <Button
                                v-if="displayedMainImage"
                                type="button"
                                variant="outline"
                                size="sm"
                                class="dashboard-modal-btn--danger"
                                @click="clearMainImageSelection"
                            >
                                Remove
                            </Button>
                        </div>
                        <input
                            ref="mainImageInput"
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            class="sr-only"
                            @change="onMainImageSelected"
                        />
                    </div>
                    <InputError :message="form.errors.image" />
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <Label>Gallery Images</Label>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            class="dashboard-modal-btn--outline"
                            @click="openGalleryPicker"
                        >
                            Add photos
                        </Button>
                    </div>

                    <input
                        ref="galleryInput"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        multiple
                        class="sr-only"
                        @change="onGalleryFilesSelected"
                    />

                    <div
                        v-if="form.existing_gallery_images.length > 0 || newGalleryPreviews.length > 0"
                        class="dashboard-gallery-grid"
                    >
                        <div
                            v-for="(imageUrl, index) in form.existing_gallery_images"
                            :key="`existing-${imageUrl}-${index}`"
                            class="dashboard-gallery-item"
                        >
                            <img :src="imageUrl" alt="Gallery image" class="dashboard-image-preview w-full" />
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                class="dashboard-gallery-item__remove dashboard-modal-btn--danger"
                                @click="removeExistingGalleryImage(index)"
                            >
                                Remove
                            </Button>
                        </div>

                        <div
                            v-for="(previewUrl, index) in newGalleryPreviews"
                            :key="`new-${previewUrl}`"
                            class="dashboard-gallery-item"
                        >
                            <img :src="previewUrl" alt="New gallery image" class="dashboard-image-preview w-full" />
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                class="dashboard-gallery-item__remove dashboard-modal-btn--danger"
                                @click="removeNewGalleryImage(index)"
                            >
                                Remove
                            </Button>
                        </div>
                    </div>

                    <p v-else class="text-sm text-muted-foreground">No gallery images yet. Add one or more photos.</p>
                    <InputError :message="form.errors.gallery_uploads ?? form.errors.existing_gallery_images" />
                </div>

                <div class="space-y-3">
                    <Label>Available Sizes</Label>
                    <div class="space-y-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Clothing</p>
                        <div class="flex flex-wrap gap-2">
                            <label
                                v-for="size in clothingSizes"
                                :key="`clothing-${size}`"
                                class="inline-flex cursor-pointer items-center gap-2 rounded-md border px-3 py-1.5 text-sm"
                                :class="form.available_sizes.includes(size) ? 'border-[#fb774b] bg-[#fff8f5]' : 'border-input'"
                            >
                                <input
                                    type="checkbox"
                                    class="rounded border-input"
                                    :checked="form.available_sizes.includes(size)"
                                    @change="toggleSize(size)"
                                />
                                {{ size }}
                            </label>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">Footwear</p>
                        <div class="flex flex-wrap gap-2">
                            <label
                                v-for="size in shoeSizes"
                                :key="`shoe-${size}`"
                                class="inline-flex cursor-pointer items-center gap-2 rounded-md border px-3 py-1.5 text-sm"
                                :class="form.available_sizes.includes(size) ? 'border-[#fb774b] bg-[#fff8f5]' : 'border-input'"
                            >
                                <input
                                    type="checkbox"
                                    class="rounded border-input"
                                    :checked="form.available_sizes.includes(size)"
                                    @change="toggleSize(size)"
                                />
                                {{ size }}
                            </label>
                        </div>
                    </div>
                    <InputError :message="form.errors.available_sizes" />
                </div>

                <InputError :message="form.errors.product" />

                <DialogFooter class="gap-2 sm:gap-0">
                    <Button type="button" variant="outline" class="dashboard-modal-btn--outline" @click="closeModal">
                        Cancel
                    </Button>
                    <Button type="submit" class="dashboard-modal-btn--primary" :disabled="form.processing">
                        {{ form.processing ? 'Saving…' : mode === 'create' ? 'Create product' : 'Save changes' }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
