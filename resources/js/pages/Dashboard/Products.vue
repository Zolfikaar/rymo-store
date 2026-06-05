<script setup lang="ts">
import ProductFormModal from '@/components/dashboard/ProductFormModal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { CatalogBrand, CatalogCategory, CatalogProduct, PaginatedProducts } from '@/types/catalog';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    products: PaginatedProducts;
    categories: { data: CatalogCategory[] };
    brands: { data: CatalogBrand[] };
    sizeOptions: string[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Products', href: '/dashboard/products' },
];

const modalOpen = ref(false);
const modalMode = ref<'create' | 'edit'>('create');
const editingProduct = ref<CatalogProduct | null>(null);

const paginationItems = computed(() => {
    const { current_page, last_page } = props.products.meta;

    return [
        { label: 'Previous', page: current_page - 1, disabled: current_page === 1, active: false },
        ...Array.from({ length: last_page }, (_, index) => {
            const page = index + 1;
            return { label: String(page), page, disabled: false, active: page === current_page };
        }),
        { label: 'Next', page: current_page + 1, disabled: current_page === last_page, active: false },
    ];
});

const showPagination = computed(() => props.products.meta.last_page > 1);

function formatCurrency(amount: string): string {
    return `$${amount}`;
}

function openCreateModal(): void {
    modalMode.value = 'create';
    editingProduct.value = null;
    modalOpen.value = true;
}

function openEditModal(product: CatalogProduct): void {
    modalMode.value = 'edit';
    editingProduct.value = product;
    modalOpen.value = true;
}

function destroyProduct(product: CatalogProduct): void {
    if (!confirm(`Delete "${product.name}"? This action cannot be undone.`)) {
        return;
    }

    router.delete(route('dashboard.products.destroy', product.id), { preserveScroll: true });
}

function goToPage(page: number): void {
    const { last_page } = props.products.meta;

    if (page < 1 || page > last_page) {
        return;
    }

    router.get(
        route('dashboard.products.index'),
        { page },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['products'],
        },
    );
}
</script>

<template>
    <Head title="Products Catalog" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="dashboard">
            <header class="dashboard__header">
                <div>
                    <h1 class="dashboard__title">Products Catalog</h1>
                    <p class="dashboard__subtitle">Manage inventory, pricing, sizing, and product media.</p>
                </div>
                <button type="button" class="dashboard__cta" @click="openCreateModal">Add product</button>
            </header>

            <section class="products-panel">
                <div class="products-panel__header">
                    <h2 class="products-panel__title">All Products</h2>
                    <p class="products-panel__meta">{{ products.meta.total }} total products</p>
                </div>

                <div v-if="products.data.length === 0" class="products-empty">
                    <p>No products yet. Create your first catalog item to get started.</p>
                </div>

                <div v-else class="products-table-wrap">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th scope="col">Image</th>
                                <th scope="col">SKU</th>
                                <th scope="col">Name</th>
                                <th scope="col">Category</th>
                                <th scope="col">Price</th>
                                <th scope="col">Stock</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in products.data" :key="product.id">
                                <td>
                                    <img
                                        v-if="product.image_url"
                                        :src="product.image_url"
                                        :alt="product.name"
                                        class="product-thumb"
                                    />
                                    <span v-else class="product-thumb product-thumb--empty">—</span>
                                </td>
                                <td>{{ product.sku }}</td>
                                <td>{{ product.name }}</td>
                                <td>{{ product.category_name ?? '—' }}</td>
                                <td>{{ formatCurrency(product.price) }}</td>
                                <td>{{ product.stock }}</td>
                                <td class="products-table__actions">
                                    <button type="button" class="link-btn" @click="openEditModal(product)">Edit</button>
                                    <button type="button" class="link-btn link-btn--danger" @click="destroyProduct(product)">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <nav v-if="showPagination" class="products-pagination" aria-label="Products pagination">
                    <button
                        v-for="item in paginationItems"
                        :key="`${item.label}-${item.page}`"
                        type="button"
                        class="products-pagination__btn"
                        :class="{ 'products-pagination__btn--active': item.active }"
                        :disabled="item.disabled"
                        @click="goToPage(item.page)"
                    >
                        {{ item.label }}
                    </button>
                </nav>
            </section>

            <ProductFormModal
                v-model:open="modalOpen"
                :mode="modalMode"
                :product="editingProduct"
                :categories="categories.data"
                :brands="brands.data"
                :size-options="sizeOptions"
            />
        </div>
    </AppLayout>
</template>

<style scoped>
.dashboard {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    padding: 1rem;
}

.dashboard__header {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
}

.dashboard__title {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 700;
    color: #1d1d1d;
}

.dashboard__subtitle {
    margin: 0.35rem 0 0;
    color: #666;
}

.dashboard__cta {
    border: none;
    border-radius: 0.5rem;
    padding: 0.625rem 1rem;
    background: #fb774b;
    color: #fff;
    font: inherit;
    font-weight: 700;
    cursor: pointer;
}

.dashboard__cta:hover {
    background: #e8663a;
}

.products-panel {
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgb(0 0 0 / 6%);
}

.products-panel__header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    background: linear-gradient(90deg, #fd8c66 0%, #fb774b 100%);
    color: #fff;
}

.products-panel__title {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 700;
}

.products-panel__meta {
    margin: 0;
    font-size: 0.875rem;
    opacity: 0.95;
}

.products-empty {
    padding: 2.5rem 1.25rem;
    text-align: center;
    color: #666;
}

.products-table-wrap {
    overflow-x: auto;
}

.products-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 920px;
}

.products-table thead {
    background: #fafafa;
}

.products-table th,
.products-table td {
    padding: 0.875rem 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

.products-table th {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #666;
}

.product-thumb {
    width: 48px;
    height: 48px;
    border-radius: 0.375rem;
    object-fit: cover;
    border: 1px solid #eee;
    display: block;
}

.product-thumb--empty {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f5f5f5;
    color: #999;
}

.products-table__actions {
    display: flex;
    gap: 0.75rem;
}

.link-btn {
    border: none;
    background: none;
    padding: 0;
    font: inherit;
    font-weight: 600;
    color: #fb774b;
    cursor: pointer;
    text-decoration: underline;
    text-underline-offset: 2px;
}

.link-btn--danger {
    color: #b42318;
}

.products-pagination {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    padding: 1rem 1.25rem;
    border-top: 1px solid #eee;
}

.products-pagination__btn {
    min-width: 2.25rem;
    padding: 0.375rem 0.75rem;
    border: 1px solid #ddd;
    border-radius: 0.375rem;
    background: #fff;
    color: #1d1d1d;
    font: inherit;
    font-weight: 600;
    cursor: pointer;
}

.products-pagination__btn:hover:not(:disabled) {
    border-color: #fb774b;
    color: #fb774b;
}

.products-pagination__btn--active {
    background: #fb774b;
    border-color: #fb774b;
    color: #fff;
}

.products-pagination__btn:disabled {
    color: #999;
    opacity: 1;
    cursor: not-allowed;
}
</style>
