<script setup lang="ts">
import { useCart } from '@/composables/useCart';
import ShopLayout from '@/layouts/ShopLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const page = usePage();

const { items: cartItems, isEmpty, subtotal, clearCart } = useCart();

const successMessage = computed(
    () => (page.props.flash as { success?: string } | undefined)?.success,
);

watch(
    successMessage,
    (message) => {
        if (message) {
            clearCart();
        }
    },
    { immediate: true },
);

const form = useForm({
    customer_info: {
        name: '',
        phone: '',
        address: '',
    },
    cart_items: [] as { id: number; quantity: number }[],
});

const shipping = computed(() => (subtotal.value > 0 ? 15 : 0));

const total = computed(() => subtotal.value + shipping.value);

function formatPrice(amount: number): string {
    return `$${amount.toFixed(2)}`;
}

function placeOrder(): void {
    if (isEmpty.value || form.processing) {
        return;
    }

    form.transform((data) => ({
        ...data,
        cart_items: cartItems.value.map((item) => ({
            id: item.id,
            quantity: item.quantity,
        })),
    })).post(route('checkout.store'), {
        preserveScroll: true,
        onSuccess: () => {
            clearCart();
            form.reset();
        },
    });
}
</script>

<template>
    <Head title="Checkout" />

    <ShopLayout>
        <section class="container pt-5 mt-5">
            <h2 class="font-weight-bold pt-5">Checkout</h2>
            <hr />
        </section>

        <section v-if="successMessage" class="checkout-success container" role="alert">
            {{ successMessage }}
        </section>

        <section v-if="isEmpty" class="checkout-empty container my-5 py-5">
            <h2 class="checkout-empty__title">Your Cart is Empty</h2>
            <p class="checkout-empty__hint">Add items to your cart before proceeding to checkout.</p>
            <Link :href="route('shop')" class="checkout-empty__cta">Continue Shopping</Link>
        </section>

        <section v-else class="container my-5 pb-5">
            <div class="checkout-grid">
                <div class="checkout-panel">
                    <h5 class="checkout-panel__title">ORDER DETAILS</h5>
                    <form class="checkout-panel__body" @submit.prevent="placeOrder">
                        <div class="checkout-field">
                            <label for="customer-name">Full Name</label>
                            <input
                                id="customer-name"
                                v-model="form.customer_info.name"
                                type="text"
                                placeholder="Your full name"
                                required
                            />
                            <p v-if="form.errors['customer_info.name']" class="field-error">
                                {{ form.errors['customer_info.name'] }}
                            </p>
                        </div>
                        <div class="checkout-field">
                            <label for="customer-phone">Phone Number</label>
                            <input
                                id="customer-phone"
                                v-model="form.customer_info.phone"
                                type="tel"
                                placeholder="Your phone number"
                                required
                            />
                            <p v-if="form.errors['customer_info.phone']" class="field-error">
                                {{ form.errors['customer_info.phone'] }}
                            </p>
                        </div>
                        <div class="checkout-field">
                            <label for="customer-address">Shipping Address</label>
                            <textarea
                                id="customer-address"
                                v-model="form.customer_info.address"
                                rows="4"
                                placeholder="Street, city, postal code"
                                required
                            />
                            <p v-if="form.errors['customer_info.address']" class="field-error">
                                {{ form.errors['customer_info.address'] }}
                            </p>
                        </div>
                        <p v-if="form.errors.cart_items" class="field-error">{{ form.errors.cart_items }}</p>

                        <div class="checkout-actions">
                            <Link :href="route('cart')" class="checkout-back">Back to Cart</Link>
                            <button type="submit" class="checkout-submit" :disabled="form.processing">
                                {{ form.processing ? 'PLACING ORDER...' : 'PLACE ORDER' }}
                            </button>
                        </div>
                    </form>
                </div>

                <div class="checkout-panel">
                    <h5 class="checkout-panel__title">ORDER SUMMARY</h5>
                    <div class="checkout-panel__body summary">
                        <ul class="summary__items">
                            <li v-for="item in cartItems" :key="item.id" class="summary__item">
                                <img :src="item.image" :alt="item.name" class="summary__image" />
                                <div class="summary__details">
                                    <p class="summary__name">{{ item.name }}</p>
                                    <p class="summary__meta">
                                        {{ formatPrice(item.price) }} &times; {{ item.quantity }}
                                    </p>
                                </div>
                                <p class="summary__line-total">{{ formatPrice(item.price * item.quantity) }}</p>
                            </li>
                        </ul>
                        <div class="summary__rows">
                            <div class="summary__row">
                                <span>Subtotal</span>
                                <span>{{ formatPrice(subtotal) }}</span>
                            </div>
                            <div class="summary__row">
                                <span>Shipping</span>
                                <span>{{ formatPrice(shipping) }}</span>
                            </div>
                            <hr class="summary__divider" />
                            <div class="summary__row summary__row--total">
                                <span>Total</span>
                                <span>{{ formatPrice(total) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </ShopLayout>
</template>

<style scoped>
.checkout-success {
    margin-top: 1rem;
    padding: 12px 16px;
    background-color: #d1e7dd;
    border: 1px solid #badbcc;
    color: #0f5132;
    font-weight: 600;
}

.checkout-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    min-height: 320px;
    gap: 1rem;
}

.checkout-empty__title {
    font-size: 2rem;
    font-weight: 700;
    color: #1d1d1d;
    margin: 0;
}

.checkout-empty__hint {
    margin: 0;
    color: #666;
}

.checkout-empty__cta {
    display: inline-block;
    background-color: #fb774b;
    color: #fff;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    text-decoration: none;
    padding: 14px 36px;
    margin-top: 1rem;
    transition: background-color 0.3s ease;
}

.checkout-empty__cta:hover {
    background-color: #e8663a;
    color: #fff;
    text-decoration: none;
}

.checkout-grid {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: 1.5rem;
}

.checkout-panel {
    flex: 1 1 calc(50% - 0.75rem);
    min-width: min(100%, 280px);
    display: flex;
    flex-direction: column;
    border: 1px solid #b6b3b3;
}

.checkout-panel__title {
    background-color: #fd8c66;
    color: #fff;
    padding: 6px 12px;
    border: none;
    font-weight: 700;
    margin: 0;
}

.checkout-panel__body {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding: 16px;
}

.checkout-field {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}

.checkout-field label {
    font-weight: 600;
    color: #2a2a2a;
    margin: 0;
}

.checkout-field input,
.checkout-field textarea {
    width: 100%;
    border: 0.5px solid #b6b3b3;
    padding: 10px 12px;
    font: inherit;
}

.checkout-field textarea {
    resize: vertical;
    min-height: 100px;
}

.field-error {
    margin: 0;
    color: #dc3545;
    font-size: 0.875rem;
}

.checkout-actions {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-top: 0.5rem;
}

.checkout-back {
    color: #2a2a2a;
    text-decoration: underline;
}

.checkout-back:hover {
    color: #fb774b;
}

.checkout-submit {
    background-color: #fb774b;
    color: #fff;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    padding: 14px 28px;
    border: none;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.checkout-submit:hover:not(:disabled) {
    background-color: #e8663a;
}

.checkout-submit:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.summary {
    gap: 1.25rem;
}

.summary__items {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.summary__item {
    display: flex;
    align-items: center;
    gap: 12px;
}

.summary__image {
    width: 56px;
    height: 56px;
    object-fit: cover;
    flex-shrink: 0;
}

.summary__details {
    flex: 1;
    min-width: 0;
}

.summary__name {
    margin: 0;
    font-weight: 600;
    color: #2a2a2a;
}

.summary__meta {
    margin: 0.25rem 0 0;
    font-size: 0.875rem;
    color: #666;
}

.summary__line-total {
    margin: 0;
    font-weight: 600;
    flex-shrink: 0;
}

.summary__rows {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.summary__row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}

.summary__row--total {
    font-weight: 700;
    font-size: 1.05rem;
}

.summary__divider {
    background-color: #b8b7b3;
    width: 100%;
    height: 1px;
    border: none;
    margin: 0.25rem 0;
}
</style>
