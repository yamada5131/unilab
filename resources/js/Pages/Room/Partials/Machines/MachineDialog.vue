<template>
    <Dialog :open="isOpen" @update:open="$emit('update:isOpen', $event)">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>{{
                    isEdit ? 'Chỉnh Sửa Máy Tính' : 'Thêm Máy Tính Mới'
                }}</DialogTitle>
                <DialogDescription>
                    {{
                        isEdit
                            ? 'Chỉnh sửa thông tin máy tính'
                            : 'Nhập thông tin máy tính mới'
                    }}
                    và nhấn Lưu khi hoàn tất.
                </DialogDescription>
            </DialogHeader>

            <form :id="formId" @submit.prevent="onSubmit" class="space-y-4">
                <FormField v-slot="{ componentField }" name="name">
                    <FormItem>
                        <FormLabel>Tên Máy</FormLabel>
                        <FormControl>
                            <Input
                                type="text"
                                placeholder="Nhập tên máy..."
                                v-bind="componentField"
                            />
                        </FormControl>
                        <FormDescription>
                            Tên hiển thị của máy tính trong hệ thống.
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="mac_address">
                    <FormItem>
                        <FormLabel>MAC Address</FormLabel>
                        <FormControl>
                            <Input
                                type="text"
                                placeholder="XX:XX:XX:XX:XX:XX"
                                v-bind="componentField"
                            />
                        </FormControl>
                        <FormDescription>
                            Địa chỉ MAC của máy tính.
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="ip_address">
                    <FormItem>
                        <FormLabel>IP Address</FormLabel>
                        <FormControl>
                            <Input
                                type="text"
                                placeholder="192.168.1.1"
                                v-bind="componentField"
                            />
                        </FormControl>
                        <FormDescription>
                            Địa chỉ IP của máy tính.
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <div class="flex gap-4">
                    <FormField v-slot="{ componentField }" name="pos_row">
                        <FormItem class="flex-1">
                            <FormLabel>Hàng</FormLabel>
                            <FormControl>
                                <Input
                                    type="number"
                                    placeholder="1"
                                    v-bind="componentField"
                                    readonly
                                />
                            </FormControl>
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="pos_col">
                        <FormItem class="flex-1">
                            <FormLabel>Cột</FormLabel>
                            <FormControl>
                                <Input
                                    type="number"
                                    placeholder="1"
                                    v-bind="componentField"
                                    readonly
                                />
                            </FormControl>
                        </FormItem>
                    </FormField>
                </div>
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
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { Machine } from '@/types';
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import { watch } from 'vue';
import * as z from 'zod';

interface ComputerFormData {
    name: string;
    mac_address: string;
    ip_address: string;
    pos_row: number;
    pos_col: number;
    room_id: string;
    id?: string;
}

interface Props {
    formId: string;
    isEdit?: boolean;
    isOpen: boolean;
    computer?: Partial<Machine>;
    roomId: string;
    position: {
        row: number;
        col: number;
    };
}

const props = withDefaults(defineProps<Props>(), {
    isOpen: false,
    isEdit: false,
    computer: () => ({
        name: '',
        mac_address: '',
        ip_address: '',
    }),
});

const emit = defineEmits<{
    submit: [value: ComputerFormData];
    close: [];
    'update:isOpen': [value: boolean];
}>();

const macAddressRegex = /^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/;
const ipAddressRegex = /^(\d{1,3}\.){3}\d{1,3}$/;

const formSchema = toTypedSchema(
    z.object({
        name: z
            .string()
            .min(2, 'Tên máy phải có ít nhất 2 ký tự')
            .max(50, 'Tên máy không được quá 50 ký tự'),
        mac_address: z
            .string()
            .regex(
                macAddressRegex,
                'MAC Address không hợp lệ (XX:XX:XX:XX:XX:XX)',
            ),
        ip_address: z
            .string()
            .regex(ipAddressRegex, 'IP Address không hợp lệ (192.168.1.1)'),
        pos_row: z.number().int().min(1),
        pos_col: z.number().int().min(1),
    }),
);

const form = useForm({
    validationSchema: formSchema,
    initialValues: {
        name: props.computer?.name || '',
        mac_address: props.computer?.mac_address || '',
        ip_address: props.computer?.ip_address || '',
        pos_row: props.position.row,
        pos_col: props.position.col,
    },
});

// Reset form values whenever the dialog opens
watch(
    () => props.isOpen,
    (isOpen) => {
        if (isOpen) {
            form.resetForm({
                values: {
                    name: props.computer?.name || '',
                    mac_address: props.computer?.mac_address || '',
                    ip_address: props.computer?.ip_address || '',
                    pos_row: props.position.row,
                    pos_col: props.position.col,
                },
            });
        }
    },
);

watch(
    () => props.position,
    (position) => {
        form.setFieldValue('pos_row', position.row);
        form.setFieldValue('pos_col', position.col);
    },
);

const onSubmit = form.handleSubmit((values) => {
    emit('submit', {
        ...values,
        room_id: props.roomId,
        ...(props.isEdit && props.computer?.id
            ? { id: props.computer.id }
            : {}),
    });
});

const closeDialog = () => {
    emit('close');
    emit('update:isOpen', false);
};
</script>
