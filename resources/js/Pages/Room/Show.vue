<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ComputerLayout from '@/Pages/Computer/ComputerLayout.vue';
import { Room } from '@/types';

defineProps<{
    room: Room;
}>();

defineOptions({
    layout: AuthenticatedLayout,
});
</script>

<template>
    <div
        class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800"
    >
        <ComputerLayout :room="room.data" />
    </div>
</template>

<style scoped></style>
