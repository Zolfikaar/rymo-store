<script setup lang="ts">
import ShopLayout from '@/layouts/ShopLayout.vue';
import type { CartItem } from '@/types/cart';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps<{
    items: CartItem[];
}>();

const cartItems = ref(
    props.items.map((item) => ({
        ...item,
    })),
);

const couponCode = ref('');

const subtotal = computed(() =>
    cartItems.value.reduce((sum, item) => sum + item.price * item.quantity, 0),
);

const shipping = computed(() => (subtotal.value > 0 ? 15 : 0));

const total = computed(() => subtotal.value + shipping.value);

function formatPrice(amount: number): string {
    return `$${amount.toFixed(2)}`;
}

function lineTotal(item: CartItem): number {
    return item.price * item.quantity;
}

function removeItem(id: number): void {
    cartItems.value = cartItems.value.filter((item) => item.id !== id);
}
</script>

<template>
    <Head title="Shopping Cart" />

    <ShopLayout>
        <section id="blog-home" class="container pt-5 mt-5">
            <h2 class="font-weight-bold pt-5">Shopping Cart</h2>
            <hr />
        </section>

        <section id="cart-container" class="container my-5">
            <table width="100%">
                <thead>
                    <tr>
                        <td>Remove</td>
                        <td>Image</td>
                        <td>Product</td>
                        <td>Price</td>
                        <td>Quantity</td>
                        <td>Total</td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in cartItems" :key="item.id">
                        <td>
                            <a href="#" @click.prevent="removeItem(item.id)">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </a>
                        </td>
                        <td><img :src="item.image" :alt="item.name" /></td>
                        <td>
                            <h5>{{ item.name }}</h5>
                        </td>
                        <td>
                            <h5>{{ formatPrice(item.price) }}</h5>
                        </td>
                        <td>
                            <input v-model.number="item.quantity" class="w-25 pl-1" type="number" min="1" />
                        </td>
                        <td>
                            <h5>{{ formatPrice(lineTotal(item)) }}</h5>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section id="cart-bottom" class="container">
            <div class="cart-bottom__grid">
                <div class="cart-panel coupon">
                    <h5 class="cart-panel__title">COUPON</h5>
                    <div class="cart-panel__body">
                        <p class="coupon__hint">Enter your coupon code if you have one</p>
                        <div class="coupon__form">
                            <input v-model="couponCode" type="text" placeholder="Coupon Code" />
                            <button type="button">APPLY COUPON</button>
                        </div>
                    </div>
                </div>

                <div class="cart-panel total">
                    <h5 class="cart-panel__title">CART TOTAL</h5>
                    <div class="cart-panel__body total__body">
                        <div class="total__rows">
                            <div class="total__row">
                                <h6>Subtotal</h6>
                                <p>{{ formatPrice(subtotal) }}</p>
                            </div>
                            <div class="total__row">
                                <h6>Shipping</h6>
                                <p>{{ formatPrice(shipping) }}</p>
                            </div>
                            <hr class="total__divider" />
                            <div class="total__row">
                                <h6>Total</h6>
                                <p>{{ formatPrice(total) }}</p>
                            </div>
                        </div>
                        <button type="button" class="total__checkout">PROCEED TO CHECKOUT</button>
                    </div>
                </div>
            </div>
        </section>
    </ShopLayout>
</template>

<style scoped>
#cart-container {
    overflow-x: auto;
}

#cart-container table {
    border-collapse: collapse;
    width: 100%;
    table-layout: fixed;
    white-space: nowrap;
}

#cart-container table thead {
    font-weight: 700;
}

#cart-container table thead td {
    background-color: #fd8c66;
    color: #fff;
    border: none;
    padding: 6px 10px;
}

#cart-container table td {
    border: 1px solid #b6b3b3;
    text-align: center;
}

#cart-container table td:nth-child(1) {
    width: 100px;
}

#cart-container table td:nth-child(2),
#cart-container table td:nth-child(3) {
    width: 200px;
}

#cart-container table td:nth-child(4),
#cart-container table td:nth-child(5),
#cart-container table td:nth-child(6) {
    width: 170px;
}

#cart-container table tbody img {
    width: 100px;
    height: 80px;
    object-fit: cover;
}

#cart-container table tbody i {
    color: #8d8c89;
}

#cart-container table tbody tr td a:hover i{
    color: #ff523b;
}

#cart-bottom .cart-bottom__grid {
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    gap: 1.5rem;
}

#cart-bottom .cart-panel {
    flex: 1 1 calc(50% - 0.75rem);
    min-width: min(100%, 280px);
    display: flex;
    flex-direction: column;
    border: 1px solid #b6b3b3;
}

#cart-bottom .cart-panel__title {
    background-color: #fd8c66;
    color: #fff;
    padding: 6px 12px;
    border: none;
    font-weight: 700;
    margin: 0;
}

#cart-bottom .cart-panel__body {
    display: flex;
    flex-direction: column;
    flex: 1;
    gap: 1rem;
    padding: 12px;
}

#cart-bottom .coupon__hint {
    margin: 0;
}

#cart-bottom .coupon__form {
    display: flex;
    align-items: stretch;
    gap: 12px;
}

#cart-bottom .coupon__form input {
    flex: 1;
    min-width: 0;
    height: 44px;
    border: 0.5px solid #b6b3b3;
    padding: 0 12px;
}

#cart-bottom .coupon__form button {
    flex-shrink: 0;
    height: 44px;
    white-space: nowrap;
}

#cart-bottom .total__body {
    gap: 1.25rem;
}

#cart-bottom .total__rows {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

#cart-bottom .total__row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

#cart-bottom .total__row h6 {
    margin: 0;
    color: #2a2a2a;
}

#cart-bottom .total__row p {
    margin: 0;
}

#cart-bottom .total__divider {
    background-color: #b8b7b3;
    width: 100%;
    height: 1px;
    border: none;
    margin: 0;
}

#cart-bottom .total__checkout {
    align-self: flex-end;
}
</style>
