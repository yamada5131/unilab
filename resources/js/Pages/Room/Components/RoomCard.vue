<template>
    <Card
        class="cursor-pointer shadow-md hover:shadow-xl"
        @click="$emit('view')"
    >
        <CardHeader>
            <CardTitle>{{ room.name }}</CardTitle>
            <CardDescription>
                Số hàng: {{ room.grid_rows }}, Số cột: {{ room.grid_cols }}
            </CardDescription>
        </CardHeader>
        <CardContent>
            <p>Thông tin chi tiết phòng sẽ hiển thị tại đây.</p>
        </CardContent>
        <CardFooter
            class="flex flex-col justify-end gap-2 px-6 pb-6 sm:flex-row sm:gap-x-2"
        >
            <Button
                @click.stop="$emit('view')"
                class="bg-blue-500 text-white hover:bg-blue-600"
            >
                Xem Chi Tiết
            </Button>
            <Button
                class="bg-yellow-500 text-white hover:bg-yellow-600"
                @click.stop="$emit('edit')"
            >
                Chỉnh sửa
            </Button>
            <Button variant="destructive" @click.stop="$emit('delete')">
                Xóa
            </Button>
        </CardFooter>
    </Card>
</template>

<script setup>
import { Button } from '@/Components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';

// Props definition
defineProps({
    room: {
        type: Object,
        required: true,
    },
});

// Events emitted by this component
defineEmits(['view', 'edit', 'delete']);
</script>
