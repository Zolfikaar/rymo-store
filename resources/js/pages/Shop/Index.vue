<script setup lang="ts">
import ProductCard from '@/components/ProductCard.vue';
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { Product } from '@/types/shop';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

defineProps<{
    products: Product[];
}>();

const currentPage = ref(1);
const totalPages = 3;

const paginationItems = computed(() => [
    {
        label: 'Previous',
        page: currentPage.value - 1,
        disabled: currentPage.value === 1,
        active: false,
    },
    ...Array.from({ length: totalPages }, (_, index) => {
        const page = index + 1;

        return {
            label: String(page),
            page,
            disabled: false,
            active: page === currentPage.value,
        };
    }),
    {
        label: 'Next',
        page: currentPage.value + 1,
        disabled: currentPage.value === totalPages,
        active: false,
    },
]);

function goToPage(page: number): void {
    if (page < 1 || page > totalPages) {
        return;
    }

    currentPage.value = page;
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
                        v-for="product in products"
                        :key="product.id"
                        :product="product"
                        :navigate-on-click="product.id === 1"
                    />

                    <nav aria-label="...">
                        <ul class="pagination mt-5">
                            <li
                                v-for="item in paginationItems"
                                :key="item.label"
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
