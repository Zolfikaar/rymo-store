export interface Product {
    id: number;
    slug: string;
    name: string;
    price: string;
    image: string;
    rating: number;
}

export interface PaginationMeta {
    current_page: number;
    from: number | null;
    last_page: number;
    per_page: number;
    to: number | null;
    total: number;
}

export interface PaginatedProducts {
    data: Product[];
    meta: PaginationMeta;
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
