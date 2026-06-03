export interface Product {
    id: number;
    name: string;
    price: string;
    image: string;
    rating: number;
    slug?: string;
}

export interface ProductDetail {
    id: number;
    slug: string;
    name: string;
    price: string;
    category: string;
    description: string;
    images: string[];
    sizes: string[];
}
