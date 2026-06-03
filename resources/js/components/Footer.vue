<script setup lang="ts">
const footerFeaturedLinks = ['Men', 'Women', 'Boys', 'Girls', 'New Arraivals', 'Shoes', 'Clothes'];

const instagramImages = [
    '/img/insta/1.jpg',
    '/img/insta/2.jpg',
    '/img/insta/3.jpg',
    '/img/insta/4.jpg',
    '/img/insta/5.jpg',
];
</script>

<template>
    <footer class="mt-5 py-5">
        <div class="row container mx-auto pt-5">
            <div class="footer-one col-lg-3 col-md-6 col-12">
                <img src="/img/logo2.png" alt="Rymo" />
                <p class="pt-3">
                    Lorem, ipsum dolor sit amet consectetur adipisicing elit. Commodi nostrum rem velit asperiores.
                    Quos, asperiores?
                </p>
            </div>
            <div class="footer-one col-lg-3 col-md-6 col-12 mb-3">
                <h5 class="pb-2">Featured</h5>
                <ul class="featured-list text-uppercase list-unstyled">
                    <li v-for="item in footerFeaturedLinks" :key="item">
                        <a href="#">{{ item }}</a>
                    </li>
                </ul>
            </div>
            <div class="footer-one col-lg-3 col-md-6 col-12">
                <h5 class="pb-2">Contact Us</h5>
                <div>
                    <h6 class="text-uppercase">Address</h6>
                    <p>123 STREET NAME, CITY, US</p>
                </div>
                <div>
                    <h6 class="text-uppercase">Phone</h6>
                    <p>(123) 456-7890</p>
                </div>
                <div>
                    <h6 class="text-uppercase">Email</h6>
                    <p>example@example.com</p>
                </div>
            </div>
            <div class="footer-one col-lg-3 col-md-6 col-12">
                <h5 class="pb-2">Instagram</h5>
                <div class="row">
                    <img
                        v-for="(image, index) in instagramImages"
                        :key="index"
                        :src="image"
                        class="img-fluid w-25 h-100 m-2"
                        alt=""
                    />
                </div>
            </div>
        </div>

        <div class="payment-and-social-media container">
            <div class="payment">
                <img src="/img/payment.png" alt="Payment methods" />
            </div>
            <div class="social-links">
                <a href="#" aria-label="Facebook"><i class="fa fa-facebook"></i></a>
                <a href="#" aria-label="Twitter"><i class="fa fa-twitter"></i></a>
                <a href="#" aria-label="LinkedIn"><i class="fa fa-linkedin"></i></a>
            </div>
        </div>
    </footer>

    <div class="copyright container">
        <div class="text-center my-3 mx-auto">
            <p>rymo eCommerce @ {{ new Date().getFullYear() }}. All rights reserved</p>
        </div>
    </div>
</template>

<style scoped>
footer {
    background-color: #222;
}

footer h5 {
    color: #d8d8d8;
    font-weight: 700;
    font-size: 1.2rem;
}

footer i {
    padding-bottom: 4px;
}

footer i a {
    font-size: 0.8rem;
    color: #999;
}

footer i a:hover {
    color: #d8d8d8;
}

footer p {
    color: #999;
    font-size: 0.8rem;
}

footer .featured-list li a {
    color: #d8d8d8;
    text-decoration: none;
}

footer .featured-list li a:hover {
    color: #fb774b;
}

footer .payment-and-social-media {
    display: flex;
    justify-content: space-between;
}

footer .payment-and-social-media .payment,
footer .payment-and-social-media .social-links {
    margin-top: 20px;
}

footer .payment-and-social-media .social-links a {
    color: #000;
    width: 38px;
    height: 38px;
    background-color: #fff;
    display: inline-block;
    text-align: center;
    line-height: 38px;
    border-radius: 50%;
    transition: 0.3s ease;
    margin: 0 5px;
}

footer .payment-and-social-media .social-links a:hover {
    color: #fff;
    background-color: #fb774b;
}

.copyright p {
    color: #999;
    font-size: 0.8rem;
}

@media only screen and (min-width: 375px) and (max-width: 762px) {
    footer .payment-and-social-media {
        align-items: center;
        margin-bottom: 0 !important;
    }
}
</style>
