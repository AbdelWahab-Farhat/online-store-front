export interface NavItem {
  label: string
  to: string
  icon: string
  description: string
}

export const adminNavigation: NavItem[] = [
  {
    label: 'المنتجات',
    to: '/products',
    icon: 'Package',
    description: 'إدارة المنتجات والمخزون',
  },
  // {
  //   label: 'الطلبات',
  //   to: '/orders',
  //   icon: 'ShoppingCart',
  //   description: 'متابعة وإدارة الطلبات',
  // },
  {
    label: 'التصنيفات',
    to: '/categories',
    icon: 'Tag',
    description: 'ترتيب أقسام المتجر',
  },
  // {
  //   label: 'الكوبونات',
  //   to: '/coupons',
  //   icon: 'Ticket',
  //   description: 'إدارة العروض والخصومات',
  // },
]
