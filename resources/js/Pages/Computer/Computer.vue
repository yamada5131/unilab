<script setup lang="ts">
import { Computer } from '@/types';
const props = defineProps<{
    index: number;
    computer: Computer;
    isSelected: boolean;
}>();
</script>

<template>
    <div
        :class="[
            'flex h-4 w-4 cursor-pointer select-none items-center justify-center rounded bg-gray-200 p-5 text-center font-semibold transition-transform hover:scale-105',
            props.computer.status === 'on' && 'border-4 border-green-500',
            props.computer.status === 'off' && 'border-4 border-red-500',
            props.computer.status === 'standby' && 'border-4 border-yellow-500',
            props.isSelected && 'scale-110 bg-blue-500 text-white shadow-lg', // Hiệu ứng khi được chọn
        ]"
    >
        {{ props.index }}
    </div>
</template>

<style scoped></style>
