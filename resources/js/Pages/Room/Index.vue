<template>
    <AuthenticatedLayout>
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
            <TableBody v-for="room in rooms" :key="room.id">
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
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Button } from '@/Components/ui/button';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Room } from '@/types';

const props = defineProps<{
    rooms: Room[];
}>();
</script>
