<script setup>
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'
import {
  NavigationMenu,
  NavigationMenuItem,
  NavigationMenuLink,
} from '@/components/ui/navigation-menu'
import { Home, Settings, BarChart, FileText, Upload, Bell, LogOut, icons } from 'lucide-vue-next'

const navItems = [
  { title: 'Home', icon: Home, href: '/' },
  { title: 'Settings', icon: Settings, href: '/settings' },
  { title: 'Statistics', icon: BarChart, href: '/stats' },
  { title: 'My Articles', icon: FileText, href: '/articles' },
  { title: 'Logout', icon: LogOut, href: '/logout' },
]
</script>

<template>
  <aside class="relative left-0 w-48 m-full border-r bg-background px-4 flex flex-col">
    <NavigationMenu class="flex-col items-start gap-1">
      <NavigationMenuItem
        v-for="item in navItems.slice(0, -1)"
        :key="item.title"
        class="list-none mt-6"
      >
        <NavigationMenuLink as-child>
          <router-link :to="item.href">
            <Button
              variant="ghost"
              class="w-full justify-start gap-3 text-muted-foreground hover:text-foreground"
            >
              <component :is="item.icon" class="h-4 w-4" />
              <span>{{ item.title }}</span>
            </Button>
          </router-link>
        </NavigationMenuLink>
      </NavigationMenuItem>

      <Separator class="mt-auto mb-2" />

      <!-- Logout Button -->
      <NavigationMenuItem class="list-none mb-6">
        <NavigationMenuLink as-child>
          <router-link :to="navItems[navItems.length - 1].href">
            <Button
              variant="ghost"
              class="w-full justify-start gap-3 text-muted-foreground hover:text-foreground"
            >
              <component :is="navItems[navItems.length - 1].icon" class="h-4 w-4" />
              <span>{{ navItems[navItems.length - 1].title }}</span>
            </Button>
          </router-link>
        </NavigationMenuLink>
      </NavigationMenuItem>
    </NavigationMenu>
  </aside>

  <!-- <div class="mb-4">
      <Button class="w-full gap-3">
        <Upload class="h-4 w-4" />
        <span>Upload Article</span>
      </Button>
    </div>

    <div class="mb-4">
      <Button variant="outline" class="w-full gap-3">
        <Bell class="h-4 w-4" />
        <span>Notifications</span>
      </Button>
    </div> -->

  <!-- Logout -->
  <!-- <Button variant="ghost" class="w-full justify-start gap-3 absolute buttom-0">
      <LogOut class="h-4 w-4" />
      <span>Logout</span>
    </Button> -->
</template>
