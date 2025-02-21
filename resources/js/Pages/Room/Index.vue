<!-- eslint-disable vue/valid-v-for -->
<template>
    <div class="relative ml-auto w-full max-w-sm items-center">
        <input
            v-model="searchModel"
            type="text"
            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 pl-10 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            placeholder="Search..."
        />
        <span
            class="absolute inset-y-0 start-0 flex items-center justify-center px-2"
        >
            <Search class="size-6 text-muted-foreground" />
        </span>
    </div>
    <Table>
        <!-- <TableCaption>A list of your recent invoices.</TableCaption> -->
        <TableHeader>
            <TableRow>
                <!-- <TableHead class="w-[100px]"> Invoice </TableHead> -->
                <TableHead>Status</TableHead>
                <TableHead>Name</TableHead>
                <TableHead>Capacity</TableHead>
                <TableHead>Actions</TableHead>
                <!-- <TableHead class="text-right"> Amount </TableHead> -->
            </TableRow>
        </TableHeader>
        <TableBody v-for="room in rooms.data" :key="room.id">
            <TableRow>
                <!-- <TableCell class="font-medium"> INV001 </TableCell> -->
                <TableCell>{{ room.status }}</TableCell>
                <TableCell>{{ room.name }}</TableCell>
                <TableCell>{{ room.capacity }}</TableCell>
                <TableCell>
                    <ResponsiveNavLink
                        class="text-green-500"
                        :href="route('rooms.show', { id: room.id })"
                    >
                        <Button variant="link"> View </Button>
                    </ResponsiveNavLink>
                </TableCell>
                <!-- <TableCell class="text-right"> $250.00 </TableCell> -->
            </TableRow>
        </TableBody>
    </Table>

    <!-- Paginatior -->
    <div class="mt-6">
        <Pagination
            :items-per-page="rooms.meta.per_page"
            :total="rooms.meta.total"
            :sibling-count="1"
            show-edges
            :default-page="rooms.meta.current_page"
        >
            <PaginationList class="flex items-center gap-1">
                <Link :href="rooms.links.first" preserve-scrol>
                    <PaginationFirst />
                </Link>
                <Link :href="rooms.links.prev" preserve-scrol>
                    <PaginationPrev />
                </Link>

                <template
                    v-for="(link, index) in rooms.meta.links.slice(1, -1)"
                    :key="index"
                >
                    <PaginationListItem v-if="link.url" as-child>
                        <Link :href="link.url" preserve-scroll>
                            <Button
                                class="h-10 w-10 p-0"
                                :variant="link.active ? 'default' : 'outline'"
                                v-html="link.label"
                            />
                        </Link>
                    </PaginationListItem>

                    <PaginationEllipsis v-else :key="`ellipsis-${index}`" />
                </template>

                <Link :href="rooms.links.next" preserve-scroll>
                    <PaginationNext />
                </Link>
                <Link :href="rooms.links.last" preserve-scroll>
                    <PaginationLast />
                </Link>
            </PaginationList>
        </Pagination>
    </div>
</template>

<script setup lang="ts">
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Button } from '@/Components/ui/button';
import {
    Pagination,
    PaginationEllipsis,
    PaginationFirst,
    PaginationLast,
    PaginationList,
    PaginationListItem,
    PaginationNext,
    PaginationPrev,
} from '@/Components/ui/pagination';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';

import type { Room } from '@/types';
import { router } from '@inertiajs/vue3';
import { watchDebounced } from '@vueuse/core';

interface RoomsData {
    total: number;
    current_page: number;
    first_page_url: string;
    prev_page_url: string;
    next_page_url: string;
    last_page_url: string;
    data: Room[];
    links: Array<{
        url: string | null;
        label: string;
        active?: boolean;
    }>;
}

const props = defineProps<{
    rooms: RoomsData;
    filters: Object;
}>();

const searchModel = defineModel<string>('searchModel', { default: '' });
searchModel.value = props.filters.search;
watchDebounced(
    searchModel,
    (searchValue) => {
        router.get(
            route('rooms.index'),
            {
                search: searchValue,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    },
    {
        debounce: 300,
    },
);

defineOptions({
    layout: AuthenticatedLayout,
});
</script>
