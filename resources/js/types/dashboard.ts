import type { PaginationMeta } from '@/types/shop';

export type OrderStatus = 'pending' | 'completed' | 'canceled';

export interface DashboardOrderItem {
    id: number;
    product_name: string;
    price_at_purchase: string;
    quantity: number;
    line_total: string;
}

export interface DashboardOrder {
    id: number;
    customer_name: string;
    customer_phone: string;
    total_price: string;
    status: OrderStatus;
    created_at: string;
    created_at_formatted: string;
    items: DashboardOrderItem[];
}

export interface DashboardStats {
    total_sales: string;
    pending_orders_count: number;
    total_products: number;
}

export interface PaginatedOrders {
    data: DashboardOrder[];
    meta: PaginationMeta;
}

export const ORDER_STATUS_OPTIONS: { value: OrderStatus; label: string }[] = [
    { value: 'pending', label: 'Pending' },
    { value: 'completed', label: 'Completed' },
    { value: 'canceled', label: 'Canceled' },
];
