// Mock product and category data for the store

export interface Category {
  id: number
  name: string
  icon: string
  slug: string
  image: string
  showOnHome: boolean
}

export interface Product {
  id: number
  name: string
  price: number
  oldPrice: number | null
  image: string
  category: string
  badge: string | null
  description: string
}

export const categories: Category[] = [
  {
    id: 1,
    name: 'حقائب',
    icon: 'mdi:bag-personal',
    slug: 'bags',
    image: 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=300&h=300&fit=crop&crop=center',
    showOnHome: true,
  },
  {
    id: 2,
    name: 'فساتين',
    icon: 'mdi:tshirt-crew',
    slug: 'dresses',
    image: 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=300&h=300&fit=crop&crop=center',
    showOnHome: true,
  },
  {
    id: 3,
    name: 'أحذية',
    icon: 'mdi:shoe-heel',
    slug: 'shoes',
    image: 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=300&h=300&fit=crop&crop=center',
    showOnHome: false,
  },
  {
    id: 4,
    name: 'مجوهرات',
    icon: 'mdi:diamond-stone',
    slug: 'jewelry',
    image: 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=300&h=300&fit=crop&crop=center',
    showOnHome: true,
  },
  {
    id: 5,
    name: 'عناية بالبشرة',
    icon: 'mdi:bottle-tonic-plus',
    slug: 'skincare',
    image: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=300&h=300&fit=crop&crop=center',
    showOnHome: false,
  },
  {
    id: 6,
    name: 'مكياج',
    icon: 'mdi:palette',
    slug: 'makeup',
    image: 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=300&h=300&fit=crop&crop=center',
    showOnHome: true,
  },
  
]

// Hero carousel slides
export const heroSlides = [
  {
    id: 1,
    title: 'تشكيلة الفساتين الجديدة',
    shortTitle: 'فساتين',
    badge: 'وصل حديثاً',
    subtitle: 'أناقة لا حدود لها لكل مناسبة',
    image: 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1400&h=600&fit=crop&crop=center',
    link: '/products?category=dresses',
  },
  {
    id: 2,
    title: 'مجموعة الحقائب الفاخرة',
    shortTitle: 'حقائب',
    badge: 'الأكثر مبيعاً',
    subtitle: 'اختاري ما يناسب ذوقك الراقي',
    image: 'https://images.unsplash.com/photo-1566150905458-1bf1fc113f0d?w=1400&h=600&fit=crop&crop=center',
    link: '/products?category=bags',
  },
  {
    id: 3,
    title: 'عالم المكياج والجمال',
    shortTitle: 'مكياج',
    badge: 'عروض حصرية',
    subtitle: 'أحدث صيحات التجميل بأسعار مميزة',
    image: 'https://images.unsplash.com/photo-1487412912498-0447578fcca8?w=1400&h=600&fit=crop&crop=center',
    link: '/products?category=makeup',
  },
  {
    id: 4,
    title: 'مجوهرات تخطف الأنظار',
    shortTitle: 'مجوهرات',
    badge: 'تشكيلة مميزة',
    subtitle: 'أضيفي لمسة ذهبية لإطلالتك',
    image: 'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=1400&h=600&fit=crop&crop=center',
    link: '/products?category=jewelry',
  },
]

export const products: Product[] = [
  {
    id: 1,
    name: 'حقيبة يد أنيقة باللون الوردي',
    price: 189,
    oldPrice: 250,
    image: 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=400&h=400&fit=crop',
    category: 'bags',
    badge: 'تخفيض',
    description: 'حقيبة يد فاخرة مصنوعة من الجلد الطبيعي بتصميم عصري وأنيق. مثالية للاستخدام اليومي والمناسبات.',
  },
  {
    id: 2,
    name: 'فستان صيفي بنقشة الزهور',
    price: 245,
    oldPrice: null,
    image: 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=400&h=400&fit=crop',
    category: 'dresses',
    badge: 'جديد',
    description: 'فستان صيفي أنيق بنقشة زهور ناعمة وقصة مريحة. مثالي للنزهات والخروجات الصيفية.',
  },
  {
    id: 3,
    name: 'حذاء كعب عالي ذهبي وردي',
    price: 320,
    oldPrice: 420,
    image: 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=400&h=400&fit=crop',
    category: 'shoes',
    badge: 'تخفيض',
    description: 'حذاء كعب عالي بلون ذهبي وردي أنيق. مصمم لإطلالة ساحرة في المناسبات الخاصة.',
  },
  {
    id: 4,
    name: 'طقم مجوهرات ذهبي فاخر',
    price: 550,
    oldPrice: 750,
    image: 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=400&h=400&fit=crop',
    category: 'jewelry',
    badge: 'تخفيض',
    description: 'طقم مجوهرات ذهبي يتضمن عقد وأقراط بتصميم كلاسيكي فاخر.',
  },
  {
    id: 5,
    name: 'مجموعة عناية بالبشرة',
    price: 175,
    oldPrice: null,
    image: 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=400&h=400&fit=crop',
    category: 'skincare',
    badge: 'جديد',
    description: 'مجموعة متكاملة للعناية بالبشرة تتضمن غسول ومرطب وسيروم بمكونات طبيعية.',
  },
  {
    id: 6,
    name: 'باليت مكياج بدرجات الروز',
    price: 120,
    oldPrice: 160,
    image: 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=400&h=400&fit=crop',
    category: 'makeup',
    badge: 'الأكثر مبيعاً',
    description: 'باليت ظلال عيون بدرجات الروز والنيود. ١٢ لون متناسق لإطلالة طبيعية وساحرة.',
  },
  {
    id: 7,
    name: 'عطر زهور الربيع الفاخر',
    price: 290,
    oldPrice: null,
    image: 'https://images.unsplash.com/photo-1541643600914-78b084683601?w=400&h=400&fit=crop',
    category: 'makeup',
    badge: 'جديد',
    description: 'عطر نسائي فاخر بمزيج من زهور الربيع والمسك الأبيض. ثبات يدوم طوال اليوم.',
  },
  {
    id: 8,
    name: 'فستان سهرة أسود فاخر',
    price: 480,
    oldPrice: 650,
    image: 'https://images.unsplash.com/photo-1566174053879-31528523f8ae?w=400&h=400&fit=crop',
    category: 'dresses',
    badge: 'تخفيض',
    description: 'فستان سهرة أسود كلاسيكي بتصميم أنيق وراقي. مثالي للمناسبات الرسمية والحفلات.',
  },
  {
    id: 9,
    name: 'حقيبة كتف جلدية كلاسيكية',
    price: 210,
    oldPrice: null,
    image: 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?w=400&h=400&fit=crop',
    category: 'bags',
    badge: 'جديد',
    description: 'حقيبة كتف أنيقة من الجلد الفاخر بتصميم كلاسيكي يناسب جميع الأوقات.',
  },
  {
    id: 10,
    name: 'خاتم ذهبي بفص ألماس',
    price: 890,
    oldPrice: 1200,
    image: 'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=400&h=400&fit=crop',
    category: 'jewelry',
    badge: 'تخفيض',
    description: 'خاتم من الذهب عيار ١٨ مرصع بفص ألماس حقيقي. هدية مثالية لكل مناسبة.',
  },
  {
    id: 11,
    name: 'أحمر شفاه مطفي وردي',
    price: 65,
    oldPrice: 90,
    image: 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?w=400&h=400&fit=crop',
    category: 'makeup',
    badge: 'تخفيض',
    description: 'أحمر شفاه مطفي بدرجة وردية أنيقة. ثبات يدوم لأكثر من ١٢ ساعة.',
  },
  {
    id: 12,
    name: 'عقد ذهبي مع لؤلؤ طبيعي',
    price: 720,
    oldPrice: null,
    image: 'https://images.unsplash.com/photo-1515562141589-67f0d999b7f3?w=400&h=400&fit=crop',
    category: 'jewelry',
    badge: 'جديد',
    description: 'عقد ذهبي فاخر مزين بلؤلؤ طبيعي. تحفة فنية تضفي رونقاً على إطلالتك.',
  },
  {
    id: 13,
    name: 'حقيبة سفر عملية أنيقة',
    price: 340,
    oldPrice: 450,
    image: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400&h=400&fit=crop',
    category: 'bags',
    badge: 'تخفيض',
    description: 'حقيبة سفر عملية وأنيقة مع مساحة تخزين واسعة. رفيقتك المثالية في السفر.',
  },
  {
    id: 14,
    name: 'فستان كاجوال مخطط',
    price: 180,
    oldPrice: null,
    image: 'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=400&h=400&fit=crop',
    category: 'dresses',
    badge: null,
    description: 'فستان كاجوال بخطوط عرضية كلاسيكية. مريح وأنيق للإطلالات اليومية.',
  },
  {
    id: 15,
    name: 'كريم أساس فاخر',
    price: 95,
    oldPrice: null,
    image: 'https://images.unsplash.com/photo-1631214524020-7e18db9a8f92?w=400&h=400&fit=crop',
    category: 'makeup',
    badge: null,
    description: 'كريم أساس بتغطية متوسطة إلى كاملة. يمنح بشرتك مظهراً طبيعياً ومشرقاً.',
  },
]
