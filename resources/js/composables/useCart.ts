import type { CartItem } from '@/types/cart';
import { computed, ref, watch } from 'vue';

const STORAGE_KEY = 'rymo-cart';

function isValidCartItem(value: unknown): value is CartItem {
    if (typeof value !== 'object' || value === null) {
        return false;
    }

    const item = value as CartItem;

    return (
        typeof item.id === 'number' &&
        typeof item.name === 'string' &&
        typeof item.price === 'number' &&
        typeof item.quantity === 'number' &&
        typeof item.image === 'string' &&
        item.quantity >= 1
    );
}

function loadCartFromStorage(): CartItem[] {
    if (typeof window === 'undefined') {
        return [];
    }

    try {
        const raw = localStorage.getItem(STORAGE_KEY);

        if (!raw) {
            return [];
        }

        const parsed: unknown = JSON.parse(raw);

        if (!Array.isArray(parsed)) {
            return [];
        }

        return parsed.filter(isValidCartItem);
    } catch {
        return [];
    }
}

function persistCart(cartItems: CartItem[]): void {
    if (typeof window === 'undefined') {
        return;
    }

    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(cartItems));
    } catch {
        // Ignore quota or privacy-mode errors.
    }
}

const items = ref<CartItem[]>(loadCartFromStorage());

watch(
    items,
    (cartItems) => {
        persistCart(cartItems);
    },
    { deep: true },
);

export function parseProductPrice(price: string): number {
    return parseFloat(price.replace(/[^0-9.]/g, '')) || 0;
}

export function useCart() {
    const itemCount = computed(() =>
        items.value.reduce((sum, item) => sum + item.quantity, 0),
    );

    const isEmpty = computed(() => items.value.length === 0);

    const subtotal = computed(() =>
        items.value.reduce((sum, item) => sum + item.price * item.quantity, 0),
    );

    function addItem(payload: Omit<CartItem, 'quantity'> & { quantity?: number }): void {
        const quantity = payload.quantity ?? 1;
        const existing = items.value.find((item) => item.id === payload.id);

        if (existing) {
            existing.quantity += quantity;
            return;
        }

        items.value.push({
            id: payload.id,
            name: payload.name,
            price: payload.price,
            image: payload.image,
            quantity,
        });
    }

    function removeItem(id: number): void {
        items.value = items.value.filter((item) => item.id !== id);
    }

    return {
        items,
        itemCount,
        isEmpty,
        subtotal,
        addItem,
        removeItem,
    };
}
