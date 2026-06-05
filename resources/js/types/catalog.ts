import type { PaginationMeta } from '@/types/shop';

export interface CatalogCategory {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    is_active: boolean;
    products_count?: number;
    created_at: string | null;
}

export interface CatalogBrand {
    id: number;
    name: string;
    slug: string;
    products_count?: number;
    created_at: string | null;
}

export interface CatalogProduct {
    id: number;
    slug: string;
    name: string;
    description: string | null;
    price: string;
    stock: number;
    sku: string;
    color: string | null;
    rating: number;
    image_url: string | null;
    gallery_images: string[];
    available_sizes: string[];
    category_id: number | null;
    category_name: string | null;
    brand_id: number | null;
    brand_name: string | null;
    created_at: string | null;
}

export interface PaginatedProducts {
    data: CatalogProduct[];
    meta: PaginationMeta;
}

export interface NameSlugFormData {
    name: string;
    slug: string;
}

export interface CategoryFormData extends NameSlugFormData {
    description: string;
    is_active: boolean;
}

export interface ProductFormData {
    name: string;
    slug: string;
    description: string;
    price: string;
    stock: string;
    sku: string;
    color: string;
    category_id: string;
    brand_id: string;
    image: File | null;
    remove_image: boolean;
    existing_gallery_images: string[];
    gallery_uploads: File[];
    available_sizes: string[];
}

export type CatalogTab = 'categories' | 'brands';

export const CLOTHING_SIZE_OPTIONS = ['S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL'] as const;
export const SHOE_SIZE_OPTIONS = ['7', '8', '9', '10', '11', '12'] as const;
