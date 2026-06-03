<script setup lang="ts">
import ProductCard from '@/components/ProductCard.vue';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { PaginatedProducts } from '@/types/shop';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    products: PaginatedProducts;
}>();

const paginationItems = computed(() => {
    const { current_page, last_page } = props.products.meta;

    return [
        {
            label: 'Previous',
            page: current_page - 1,
            disabled: current_page === 1,
            active: false,
        },
        ...Array.from({ length: last_page }, (_, index) => {
            const page = index + 1;

            return {
                label: String(page),
                page,
                disabled: false,
                active: page === current_page,
            };
        }),
        {
            label: 'Next',
            page: current_page + 1,
            disabled: current_page === last_page,
            active: false,
        },
    ];
});

const showPagination = computed(() => props.products.meta.last_page > 1);

function goToPage(page: number): void {
    const { last_page } = props.products.meta;

    if (page < 1 || page > last_page) {
        return;
    }

    router.get(
        route('shop'),
        { page },
        {
            preserveState: true,
            only: ['products'],
        },
    );
}
</script>

<template>
    <Head title="Shop" />

    <ShopLayout>
        <section id="featured" class="my-5 py-5">
            <div class="container">
                <div class="mt-5 py-5">
                    <h2 class="font-weight-bold">Our Featured</h2>
                    <hr>
                    <p>Here you can check out our new products with fair price on rymo.</p>
                </div>

                <div class="row mx-auto">
                    <ProductCard
                        v-for="product in products.data"
                        :key="product.slug"
                        :product="product"
                    />

                    <nav v-if="showPagination" aria-label="Shop pagination">
                        <ul class="pagination mt-5">
                            <li
                                v-for="item in paginationItems"
                                :key="`${item.label}-${item.page}`"
                                class="page-item"
                                :class="{ active: item.active, disabled: item.disabled }"
                            >
                                <a
                                    class="page-link"
                                    href="#"
                                    @click.prevent="!item.disabled && goToPage(item.page)"
                                >
                                    {{ item.label }}
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </section>
    </ShopLayout>
</template>

<style scoped>
#featured .pagination .page-link {
    color: #1d1d1d;
}

#featured .pagination .page-item.active .page-link {
    background-color: coral;
    border-color: coral;
    color: #fff;
}

#featured .pagination .page-item:not(.disabled):hover .page-link {
    background-color: coral;
    border-color: coral;
    color: #fff;
}

#featured .pagination .page-item.disabled .page-link {
    color: #999;
}
</style>
