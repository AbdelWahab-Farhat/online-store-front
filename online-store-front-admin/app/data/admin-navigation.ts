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
  {
    label: 'الإعلانات',
    to: '/announcements',
    icon: 'Megaphone',
    description: 'إدارة اللوحات الإعلانية',
  },
  {
    label: 'الإعدادات',
    to: '/settings',
    icon: 'Settings',
    description: 'إعدادات المتجر العامة',
  },
  // {
  //   label: 'الكوبونات',
  //   to: '/coupons',
  //   icon: 'Ticket',
  //   description: 'إدارة العروض والخصومات',
  // },
]
