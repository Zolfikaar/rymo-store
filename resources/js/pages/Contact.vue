<script setup lang="ts">
import ShopLayout from '@/layouts/ShopLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const successMessage = computed(
    () => (page.props.flash as { success?: string } | undefined)?.success,
);

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
});

function submit(): void {
    form.post(route('contact.submit'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <Head title="Contact Us" />

    <ShopLayout>
        <section class="container pt-5 mt-5">
            <h2 class="font-weight-bold pt-5">Contact Us</h2>
            <hr />
            <p class="mb-5">
                Have a question about your order, sizing, or returns? Send us a message and the rymo team will get back
                to you shortly.
            </p>
        </section>

        <section class="container pb-5">
            <div v-if="successMessage" class="alert alert-success contact-alert" role="alert">
                {{ successMessage }}
            </div>

            <div class="row">
                <div class="col-lg-5 col-md-12 mb-5">
                    <div class="contact-info-card p-4 h-100">
                        <h5 class="pb-3">Get In Touch</h5>
                        <div class="mb-4">
                            <h6 class="text-uppercase">Address</h6>
                            <p class="mb-0">123 STREET NAME, CITY, US</p>
                        </div>
                        <div class="mb-4">
                            <h6 class="text-uppercase">Phone</h6>
                            <p class="mb-0">(123) 456-7890</p>
                        </div>
                        <div class="mb-4">
                            <h6 class="text-uppercase">Email</h6>
                            <p class="mb-0">example@example.com</p>
                        </div>
                        <div class="contact-social-links">
                            <a href="#" aria-label="Facebook"><i class="fa fa-facebook"></i></a>
                            <a href="#" aria-label="Twitter"><i class="fa fa-twitter"></i></a>
                            <a href="#" aria-label="LinkedIn"><i class="fa fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7 col-md-12">
                    <form class="contact-form p-4" @submit.prevent="submit">
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="form-control"
                                placeholder="Your name"
                                required
                            />
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="form-control"
                                placeholder="you@example.com"
                                required
                            />
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input
                                id="subject"
                                v-model="form.subject"
                                type="text"
                                class="form-control"
                                placeholder="How can we help?"
                                required
                            />
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea
                                id="message"
                                v-model="form.message"
                                class="form-control"
                                rows="5"
                                placeholder="Write your message here..."
                                required
                            ></textarea>
                        </div>
                        <button type="submit" class="buy-btn" :disabled="form.processing">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </ShopLayout>
</template>

<style scoped>
.contact-info-card,
.contact-form {
    border: 1px solid #e5e5e5;
    background-color: #fff;
}

.contact-info-card h5,
.contact-info-card h6 {
    color: #1d1d1d;
}

.contact-info-card h6 {
    color: #fb774b;
    font-weight: 600;
}

.contact-info-card p {
    color: #666;
    font-size: 0.9rem;
}

.contact-social-links a {
    color: #000;
    width: 38px;
    height: 38px;
    background-color: #f5f5f5;
    display: inline-block;
    text-align: center;
    line-height: 38px;
    border-radius: 50%;
    transition: 0.3s ease;
    margin-right: 8px;
}

.contact-social-links a:hover {
    color: #fff;
    background-color: coral;
}

.contact-form label {
    font-weight: 600;
    color: #1d1d1d;
    font-size: 0.85rem;
    text-transform: uppercase;
}

.contact-form .form-control {
    border: 1px solid #d8d8d8;
    border-radius: 0;
    padding: 12px 14px;
    margin-bottom: 1rem;
}

.contact-form .form-control:focus {
    border-color: coral;
    box-shadow: none;
    outline: none;
}

.contact-form .buy-btn {
    background-color: #fb774b;
    opacity: 1;
}

.contact-alert {
    border-radius: 0;
    border-color: coral;
    background-color: #fff5f2;
    color: #1d1d1d;
}
</style>
