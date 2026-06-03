export interface BlogPost {
    id: number;
    image: string;
    title: string;
    date?: string;
    size: 'large' | 'medium' | 'small' | 'banner';
}
