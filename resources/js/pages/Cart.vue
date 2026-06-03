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
            <div class="row">
                <div class="coupon col-lg-6 col-md-6 col-12 mb-4">
                    <div>
                        <h5>COUPON</h5>
                        <p>Enter your coupon code if you have one</p>
                        <input v-model="couponCode" type="text" placeholder="Coupon Code" />
                        <button type="button">APPLY COUPON</button>
                    </div>
                </div>

                <div class="total col-lg-6 col-md-6 col-12">
                    <div>
                        <h5>CART TOTAL</h5>
                        <div class="d-flex justify-content-between">
                            <h6>Subtotal</h6>
                            <p>{{ formatPrice(subtotal) }}</p>
                        </div>
                        <div class="d-flex justify-content-between">
                            <h6>Shipping</h6>
                            <p>{{ formatPrice(shipping) }}</p>
                        </div>
                        <hr class="second-hr" />
                        <div class="d-flex justify-content-between">
                            <h6>Total</h6>
                            <p>{{ formatPrice(total) }}</p>
                        </div>

                        <button type="button" class="ml-auto">PROCEED TO CHECKOUT</button>
                    </div>
                </div>
            </div>
        </section>
    </ShopLayout>
</template>

<style scoped>

#cart-container table tbody tr td a:hover i{
    color: #ff523b;
}
</style>
