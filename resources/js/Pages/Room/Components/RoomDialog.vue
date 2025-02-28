<template>
    <Dialog :open="isOpen" @update:open="$emit('update:isOpen', $event)">
        <DialogTrigger v-if="!isOpen && showTrigger">
            <Button
                class="bg-blue-500 text-white hover:bg-blue-600"
                @click="$emit('update:isOpen', true)"
            >
                {{ isEdit ? 'Chỉnh sửa' : 'Thêm Phòng' }}
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>{{
                    isEdit ? 'Chỉnh Sửa Phòng' : 'Thêm Phòng Mới'
                }}</DialogTitle>
                <DialogDescription>
                    {{
                        isEdit
                            ? 'Chỉnh sửa thông tin phòng'
                            : 'Nhập thông tin phòng mới'
                    }}
                    và nhấn Lưu khi hoàn tất.
                </DialogDescription>
            </DialogHeader>

            <form :id="formId" @submit.prevent="onSubmit" class="space-y-4">
                <FormField v-slot="{ componentField }" name="name">
                    <FormItem>
                        <FormLabel>Tên Phòng</FormLabel>
                        <FormControl>
                            <Input
                                type="text"
                                placeholder="Nhập tên phòng..."
                                v-bind="componentField"
                            />
                        </FormControl>
                        <FormDescription>
                            Đây là tên hiển thị của phòng trong hệ thống.
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="grid_rows">
                    <FormItem>
                        <FormLabel>Số hàng</FormLabel>
                        <FormControl>
                            <Input
                                type="number"
                                placeholder="Nhập số hàng..."
                                v-bind="componentField"
                            />
                        </FormControl>
                        <FormDescription>
                            Số hàng trong sơ đồ bố trí máy tính của phòng.
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="grid_cols">
                    <FormItem>
                        <FormLabel>Số cột</FormLabel>
                        <FormControl>
                            <Input
                                type="number"
                                placeholder="Nhập số cột..."
                                v-bind="componentField"
                            />
                        </FormControl>
                        <FormDescription>
                            Số cột trong sơ đồ bố trí máy tính của phòng.
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>
            </form>

            <DialogFooter>
                <Button type="button" variant="outline" @click="closeDialog">
                    Hủy
                </Button>
                <Button type="submit" :form="formId">Lưu</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Button } from '@/Components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/Components/ui/dialog';
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/Components/ui/form';
import { Input } from '@/Components/ui/input';
import { Room } from '@/types';
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import { watch } from 'vue';
import * as z from 'zod';

interface RoomFormData {
    name: string;
    grid_rows: number;
    grid_cols: number;
    id?: string;
}

interface Props {
    formId: string;
    isEdit: boolean;
    isOpen: boolean;
    room?: Partial<Room>;
    showTrigger?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    isOpen: false,
    isEdit: false,
    room: () => ({ name: '', grid_rows: 5, grid_cols: 5 }),
    showTrigger: false,
});

const emit = defineEmits<{
    submit: [value: RoomFormData];
    close: [];
    'update:isOpen': [value: boolean];
}>();

const formSchema = toTypedSchema(
    z.object({
        name: z
            .string()
            .min(2, 'Tên phòng phải có ít nhất 2 ký tự')
            .max(50, 'Tên phòng không được quá 50 ký tự'),
        grid_rows: z
            .number()
            .int()
            .min(1, 'Số hàng phải lớn hơn 0')
            .max(20, 'Số hàng không được vượt quá 20'),
        grid_cols: z
            .number()
            .int()
            .min(1, 'Số cột phải lớn hơn 0')
            .max(20, 'Số cột không được vượt quá 20'),
    }),
);

const form = useForm({
    validationSchema: formSchema,
    initialValues: {
        name: props.room?.name || '',
        grid_rows: props.room?.grid_rows || 5,
        grid_cols: props.room?.grid_cols || 5,
    },
});

// Reset form values whenever the dialog opens
watch(
    () => props.isOpen,
    (isOpen) => {
        if (isOpen) {
            form.resetForm({
                values: {
                    name: props.room?.name || '',
                    grid_rows: props.room?.grid_rows || 5,
                    grid_cols: props.room?.grid_cols || 5,
                },
            });
        }
    },
);

const onSubmit = form.handleSubmit((values) => {
    emit('submit', values);
});

const closeDialog = () => {
    emit('close');
    emit('update:isOpen', false);
};
</script>
