<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {
    ORDER_STATUS_OPTIONS,
    type DashboardOrder,
    type DashboardStats,
    type OrderStatus,
    type PaginatedOrders,
} from '@/types/dashboard';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    orders: PaginatedOrders;
    stats: DashboardStats;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const expandedOrderId = ref<number | null>(null);

const paginationItems = computed(() => {
    const { current_page, last_page } = props.orders.meta;

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

const showPagination = computed(() => props.orders.meta.last_page > 1);

function formatCurrency(amount: string): string {
    return `$${amount}`;
}

function toggleOrderDetails(orderId: number): void {
    expandedOrderId.value = expandedOrderId.value === orderId ? null : orderId;
}

function isExpanded(orderId: number): boolean {
    return expandedOrderId.value === orderId;
}

function statusBadgeClass(status: OrderStatus): string {
    switch (status) {
        case 'completed':
            return 'status-badge status-badge--completed';
        case 'canceled':
            return 'status-badge status-badge--canceled';
        default:
            return 'status-badge status-badge--pending';
    }
}

function updateStatus(order: DashboardOrder, status: OrderStatus): void {
    if (status === order.status) {
        return;
    }

    router.patch(route('dashboard.orders.update-status', order.id), { status }, { preserveScroll: true });
}

function goToPage(page: number): void {
    const { last_page } = props.orders.meta;

    if (page < 1 || page > last_page) {
        return;
    }

    router.get(
        route('dashboard'),
        { page },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['orders', 'stats'],
        },
    );
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="dashboard">
            <header class="dashboard__header">
                <h1 class="dashboard__title">Order Management</h1>
                <p class="dashboard__subtitle">Review guest checkout orders and update fulfillment status.</p>
            </header>

            <div class="stats-grid">
                <article class="stat-card">
                    <p class="stat-card__label">Total Sales</p>
                    <p class="stat-card__value">{{ formatCurrency(stats.total_sales) }}</p>
                    <p class="stat-card__hint">Completed orders only</p>
                </article>
                <article class="stat-card">
                    <p class="stat-card__label">Pending Orders</p>
                    <p class="stat-card__value">{{ stats.pending_orders_count }}</p>
                    <p class="stat-card__hint">Awaiting review or fulfillment</p>
                </article>
                <article class="stat-card">
                    <p class="stat-card__label">Total Products</p>
                    <p class="stat-card__value">{{ stats.total_products }}</p>
                    <p class="stat-card__hint">Active catalog items</p>
                </article>
            </div>

            <section class="orders-panel">
                <div class="orders-panel__header">
                    <h2 class="orders-panel__title">Recent Orders</h2>
                    <p class="orders-panel__meta">{{ orders.meta.total }} total orders</p>
                </div>

                <div v-if="orders.data.length === 0" class="orders-empty">
                    <p>No orders yet. Guest checkout submissions will appear here.</p>
                </div>

                <div v-else class="orders-table-wrap">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Total</th>
                                <th scope="col">Status</th>
                                <th scope="col">Date</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="order in orders.data" :key="order.id">
                                <tr
                                    class="orders-table__row"
                                    :class="{ 'orders-table__row--expanded': isExpanded(order.id) }"
                                >
                                    <td>
                                        <button
                                            type="button"
                                            class="order-id-btn"
                                            :aria-expanded="isExpanded(order.id)"
                                            @click="toggleOrderDetails(order.id)"
                                        >
                                            #{{ order.id }}
                                        </button>
                                    </td>
                                    <td>{{ order.customer_name }}</td>
                                    <td>{{ order.customer_phone }}</td>
                                    <td>{{ formatCurrency(order.total_price) }}</td>
                                    <td>
                                        <span :class="statusBadgeClass(order.status)">
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td>{{ order.created_at_formatted }}</td>
                                    <td>
                                        <select
                                            class="status-select"
                                            :value="order.status"
                                            @change="
                                                updateStatus(
                                                    order,
                                                    ($event.target as HTMLSelectElement).value as OrderStatus,
                                                )
                                            "
                                        >
                                            <option
                                                v-for="option in ORDER_STATUS_OPTIONS"
                                                :key="option.value"
                                                :value="option.value"
                                            >
                                                {{ option.label }}
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <tr v-if="isExpanded(order.id)" class="orders-table__details-row">
                                    <td colspan="7">
                                        <div class="order-details">
                                            <h3 class="order-details__title">Order Items</h3>
                                            <table class="items-table">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Product</th>
                                                        <th scope="col">Price</th>
                                                        <th scope="col">Qty</th>
                                                        <th scope="col">Line Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="item in order.items" :key="item.id">
                                                        <td>{{ item.product_name }}</td>
                                                        <td>{{ formatCurrency(item.price_at_purchase) }}</td>
                                                        <td>{{ item.quantity }}</td>
                                                        <td>{{ formatCurrency(item.line_total) }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <nav v-if="showPagination" class="orders-pagination" aria-label="Orders pagination">
                    <button
                        v-for="item in paginationItems"
                        :key="`${item.label}-${item.page}`"
                        type="button"
                        class="orders-pagination__btn"
                        :class="{ 'orders-pagination__btn--active': item.active }"
                        :disabled="item.disabled"
                        @click="goToPage(item.page)"
                    >
                        {{ item.label }}
                    </button>
                </nav>
            </section>
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

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
}

.stat-card {
    background: #fff;
    border: 1px solid #e5e5e5;
    border-top: 4px solid #fd8c66;
    border-radius: 0.75rem;
    padding: 1.25rem;
    box-shadow: 0 1px 3px rgb(0 0 0 / 6%);
}

.stat-card__label {
    margin: 0;
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #666;
}

.stat-card__value {
    margin: 0.5rem 0 0;
    font-size: 2rem;
    font-weight: 700;
    color: #1d1d1d;
}

.stat-card__hint {
    margin: 0.35rem 0 0;
    font-size: 0.875rem;
    color: #888;
}

.orders-panel {
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgb(0 0 0 / 6%);
}

.orders-panel__header {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    background: linear-gradient(90deg, #fd8c66 0%, #fb774b 100%);
    color: #fff;
}

.orders-panel__title {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 700;
}

.orders-panel__meta {
    margin: 0;
    font-size: 0.875rem;
    opacity: 0.95;
}

.orders-empty {
    padding: 2.5rem 1.25rem;
    text-align: center;
    color: #666;
}

.orders-table-wrap {
    overflow-x: auto;
}

.orders-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 860px;
}

.orders-table thead {
    background: #fafafa;
}

.orders-table th,
.orders-table td {
    padding: 0.875rem 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

.orders-table th {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: #666;
}

.orders-table__row {
    transition: background-color 0.15s ease;
}

.orders-table__row:hover {
    background: #fff8f5;
}

.orders-table__row--expanded {
    background: #fff8f5;
}

.order-id-btn {
    background: none;
    border: none;
    padding: 0;
    font: inherit;
    font-weight: 700;
    color: #fb774b;
    cursor: pointer;
    text-decoration: underline;
    text-underline-offset: 2px;
}

.order-id-btn:hover {
    color: #e8663a;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.625rem;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.03em;
    text-transform: capitalize;
}

.status-badge--pending {
    background: #fff3cd;
    color: #856404;
}

.status-badge--completed {
    background: #d1e7dd;
    color: #0f5132;
}

.status-badge--canceled {
    background: #f8d7da;
    color: #842029;
}

.status-select {
    min-width: 120px;
    border: 1px solid #d0d0d0;
    border-radius: 0.375rem;
    padding: 0.375rem 0.5rem;
    font: inherit;
    background: #fff;
}

.orders-table__details-row td {
    background: #fcfcfc;
    padding-top: 0;
}

.order-details {
    padding: 0.5rem 0 1rem;
}

.order-details__title {
    margin: 0 0 0.75rem;
    font-size: 0.95rem;
    font-weight: 700;
    color: #2a2a2a;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
}

.items-table th,
.items-table td {
    padding: 0.625rem 0.75rem;
    text-align: left;
    border: 1px solid #ececec;
}

.items-table thead {
    background: #f5f5f5;
}

.items-table th {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: #666;
}

.orders-pagination {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    padding: 1rem 1.25rem;
    border-top: 1px solid #eee;
}

.orders-pagination__btn {
    min-width: 2.25rem;
    padding: 0.375rem 0.75rem;
    border: 1px solid #ddd;
    border-radius: 0.375rem;
    background: #fff;
    font: inherit;
    cursor: pointer;
}

.orders-pagination__btn:hover:not(:disabled) {
    border-color: #fb774b;
    color: #fb774b;
}

.orders-pagination__btn--active {
    background: #fb774b;
    border-color: #fb774b;
    color: #fff;
}

.orders-pagination__btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
