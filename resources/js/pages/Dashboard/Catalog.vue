<script setup lang="ts">
import CatalogFormModal from '@/components/dashboard/CatalogFormModal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { CatalogBrand, CatalogCategory, CatalogTab } from '@/types/catalog';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    activeTab: CatalogTab;
    categories: { data: CatalogCategory[] };
    brands: { data: CatalogBrand[] };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Categories & Brands', href: '/dashboard/categories' },
];

const modalOpen = ref(false);
const modalMode = ref<'create' | 'edit'>('create');
const modalResourceType = ref<'category' | 'brand'>('category');
const editingItem = ref<CatalogCategory | CatalogBrand | null>(null);

function openCreateModal(resourceType: 'category' | 'brand'): void {
    modalResourceType.value = resourceType;
    modalMode.value = 'create';
    editingItem.value = null;
    modalOpen.value = true;
}

function openEditModal(resourceType: 'category' | 'brand', item: CatalogCategory | CatalogBrand): void {
    modalResourceType.value = resourceType;
    modalMode.value = 'edit';
    editingItem.value = item;
    modalOpen.value = true;
}

function destroyItem(resourceType: 'category' | 'brand', id: number): void {
    if (!confirm(`Delete this ${resourceType}? This action cannot be undone.`)) {
        return;
    }

    const routeName =
        resourceType === 'category' ? 'dashboard.categories.destroy' : 'dashboard.brands.destroy';

    router.delete(route(routeName, id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Categories & Brands" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="dashboard">
            <header class="dashboard__header">
                <h1 class="dashboard__title">Categories & Brands</h1>
                <p class="dashboard__subtitle">Organize your catalog taxonomy and product manufacturers.</p>
            </header>

            <div class="catalog-tabs">
                <Link
                    :href="route('dashboard.categories.index')"
                    class="catalog-tabs__btn"
                    :class="{ 'catalog-tabs__btn--active': activeTab === 'categories' }"
                >
                    Categories
                </Link>
                <Link
                    :href="route('dashboard.brands.index')"
                    class="catalog-tabs__btn"
                    :class="{ 'catalog-tabs__btn--active': activeTab === 'brands' }"
                >
                    Brands
                </Link>
            </div>

            <section v-if="activeTab === 'categories'" class="catalog-panel">
                <div class="catalog-panel__header">
                    <div>
                        <h2 class="catalog-panel__title">Categories</h2>
                        <p class="catalog-panel__meta">{{ categories.data.length }} total</p>
                    </div>
                    <button type="button" class="catalog-panel__action" @click="openCreateModal('category')">
                        Add category
                    </button>
                </div>

                <div class="catalog-table-wrap">
                    <table class="catalog-table">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Slug</th>
                                <th scope="col">Products</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="category in categories.data" :key="category.id">
                                <td>{{ category.name }}</td>
                                <td>{{ category.slug }}</td>
                                <td>{{ category.products_count ?? 0 }}</td>
                                <td>
                                    <span
                                        class="status-pill"
                                        :class="category.is_active ? 'status-pill--active' : 'status-pill--inactive'"
                                    >
                                        {{ category.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="catalog-table__actions">
                                    <button type="button" class="link-btn" @click="openEditModal('category', category)">
                                        Edit
                                    </button>
                                    <button type="button" class="link-btn link-btn--danger" @click="destroyItem('category', category.id)">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section v-else class="catalog-panel">
                <div class="catalog-panel__header">
                    <div>
                        <h2 class="catalog-panel__title">Brands</h2>
                        <p class="catalog-panel__meta">{{ brands.data.length }} total</p>
                    </div>
                    <button type="button" class="catalog-panel__action" @click="openCreateModal('brand')">
                        Add brand
                    </button>
                </div>

                <div class="catalog-table-wrap">
                    <table class="catalog-table">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Slug</th>
                                <th scope="col">Products</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="brand in brands.data" :key="brand.id">
                                <td>{{ brand.name }}</td>
                                <td>{{ brand.slug }}</td>
                                <td>{{ brand.products_count ?? 0 }}</td>
                                <td class="catalog-table__actions">
                                    <button type="button" class="link-btn" @click="openEditModal('brand', brand)">
                                        Edit
                                    </button>
                                    <button type="button" class="link-btn link-btn--danger" @click="destroyItem('brand', brand.id)">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <CatalogFormModal
                :key="`${modalResourceType}-${modalMode}-${editingItem?.id ?? 'new'}`"
                v-model:open="modalOpen"
                :mode="modalMode"
                :resource-type="modalResourceType"
                :item="editingItem"
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
    flex-direction: column;
    gap: 0.35rem;
}

.dashboard__title {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 700;
    color: #1d1d1d;
}

.dashboard__subtitle {
    margin: 0;
    color: #666;
}

.catalog-tabs {
    display: flex;
    gap: 0.5rem;
}

.catalog-tabs__btn {
    padding: 0.5rem 1rem;
    border: 1px solid #ddd;
    border-radius: 0.5rem;
    background: #fff;
    color: #444;
    font: inherit;
    font-weight: 600;
    text-decoration: none;
}

.catalog-tabs__btn--active {
    background: #fb774b;
    border-color: #fb774b;
    color: #fff;
}

.catalog-panel {
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgb(0 0 0 / 6%);
}

.catalog-panel__header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    background: linear-gradient(90deg, #fd8c66 0%, #fb774b 100%);
    color: #fff;
}

.catalog-panel__title {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 700;
}

.catalog-panel__meta {
    margin: 0.25rem 0 0;
    font-size: 0.875rem;
    opacity: 0.95;
}

.catalog-panel__action {
    border: 1px solid rgb(255 255 255 / 40%);
    border-radius: 0.375rem;
    padding: 0.5rem 0.875rem;
    background: rgb(255 255 255 / 15%);
    color: #fff;
    font: inherit;
    font-weight: 600;
    cursor: pointer;
}

.catalog-panel__action:hover {
    background: rgb(255 255 255 / 25%);
}

.catalog-table-wrap {
    overflow-x: auto;
}

.catalog-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 640px;
}

.catalog-table thead {
    background: #fafafa;
}

.catalog-table th,
.catalog-table td {
    padding: 0.875rem 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

.catalog-table th {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #666;
}

.catalog-table__actions {
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

.status-pill {
    display: inline-block;
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 700;
}

.status-pill--active {
    background: #d1e7dd;
    color: #0f5132;
}

.status-pill--inactive {
    background: #f8d7da;
    color: #842029;
}
</style>
