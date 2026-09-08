<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { cn } from "@/lib/utils"
import { Sheet, SheetContent } from '@/components/ui/sheet'
import SheetDescription from '@/components/ui/sheet/SheetDescription.vue'
import SheetHeader from '@/components/ui/sheet/SheetHeader.vue'
import SheetTitle from '@/components/ui/sheet/SheetTitle.vue'
import { SIDEBAR_WIDTH_MOBILE, useSidebar } from "./utils"

type SidebarPropsLocal = {
  side?: "left" | "right"
  variant?: "sidebar" | "floating" | "inset"
  collapsible?: "offcanvas" | "icon" | "none"
  class?: HTMLAttributes["class"]
}

defineOptions({ inheritAttrs: false })

const props = withDefaults(defineProps<SidebarPropsLocal>(), {
  side: "left",
  variant: "sidebar",
  collapsible: "offcanvas",
})

const { isMobile, state, openMobile, setOpenMobile } = useSidebar()
</script>

<template>
  <div
    v-if="collapsible === 'none'"
    data-slot="sidebar"
    :class="cn('bg-sidebar text-sidebar-foreground flex h-svh w-(--sidebar-width) shrink-0 flex-col', props.class)"
    v-bind="$attrs"
  >
    <slot />
  </div>

  <Sheet v-else-if="isMobile" :open="openMobile" v-bind="$attrs" @update:open="setOpenMobile">
    <SheetContent
      data-sidebar="sidebar"
      data-slot="sidebar"
      data-mobile="true"
      :side="side"
      class="bg-sidebar text-sidebar-foreground w-(--sidebar-width) p-0 [&>button]:hidden"
      :style="{ '--sidebar-width': SIDEBAR_WIDTH_MOBILE }"
    >
      <SheetHeader class="sr-only">
        <SheetTitle>Sidebar</SheetTitle>
        <SheetDescription>Displays the mobile sidebar.</SheetDescription>
      </SheetHeader>
      <div class="flex h-full w-full flex-col">
        <slot />
      </div>
    </SheetContent>
  </Sheet>

  <div
    v-else
    class="group peer sticky top-0 z-10 flex h-svh shrink-0 w-(--sidebar-width) transition-[margin,width] duration-200 ease-linear"
    data-slot="sidebar"
    :data-state="state"
    :data-collapsible="state === 'collapsed' ? collapsible : ''"
    :data-variant="variant"
    :data-side="side"
    :class="cn(
      side === 'left'
        ? 'data-[collapsible=offcanvas]:-ml-[var(--sidebar-width)]'
        : 'data-[collapsible=offcanvas]:-mr-[var(--sidebar-width)]',
      variant === 'floating' || variant === 'inset'
        ? 'p-2 data-[collapsible=icon]:w-[calc(var(--sidebar-width-icon)+(--spacing(4))+2px)]'
        : 'data-[collapsible=icon]:w-(--sidebar-width-icon) data-[side=left]:border-r data-[side=right]:border-l',
      props.class,
    )"
    v-bind="$attrs"
  >
    <div
      data-sidebar="sidebar"
      class="bg-sidebar group-data-[variant=floating]:border-sidebar-border flex h-full w-full flex-col group-data-[variant=floating]:rounded-lg group-data-[variant=floating]:border group-data-[variant=floating]:shadow-sm"
    >
      <slot />
    </div>
  </div>
</template>
