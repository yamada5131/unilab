<script setup lang="ts">
import { Button } from '@/Components/ui/button';
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/Components/ui/form';
import { Textarea } from '@/Components/ui/textarea';
import { useToast } from '@/Components/ui/toast/use-toast';
import Computer from '@/Pages/Computer/Computer.vue';
import { Room } from '@/types';

const { toast } = useToast();

import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import { computed, h, reactive } from 'vue';
import * as z from 'zod';

const formSchema = toTypedSchema(
    z.object({
        command: z
            .string()
            .min(10, {
                message: 'Command must be at least 10 characters.',
            })
            .max(160, {
                message: 'Command must not be longer than 30 characters.',
            }),
    }),
);

const { handleSubmit } = useForm({
    validationSchema: formSchema,
});

import { router } from '@inertiajs/vue3';

const props = defineProps<{
    room: Room;
}>();

const selectedComputers = reactive<string[]>([]);

const handleClick = (id: string, event: MouseEvent) => {
    const isSelected = selectedComputers.includes(id);

    // Không nhấn Ctrl: Chỉ chọn một phần tử
    if (!event.ctrlKey) {
        selectedComputers.splice(0, selectedComputers.length, id);
        if (isSelected) {
            selectedComputers.splice(0, 1); // Xóa nếu đã chọn
        }
        return;
    }

    // Nhấn Ctrl: Thêm hoặc xóa phần tử
    if (isSelected) {
        selectedComputers.splice(selectedComputers.indexOf(id), 1);
    } else {
        selectedComputers.push(id);
    }
};

const selectedComputer = computed(() => {
    if (selectedComputers.length === 1) {
        return props.room.computers.find((c) => c.id === selectedComputers[0]);
    }
    return null;
});

const onSubmit = handleSubmit((values) => {
    toast({
        title: 'You submitted the following values:',
        description: h(
            'pre',
            { class: 'mt-2 w-[340px] rounded-md bg-slate-950 p-4' },
            h('code', { class: 'text-white' }, JSON.stringify(values, null, 2)),
        ),
    });
    router.post(
        route('computers.command', {
            id: selectedComputer.value?.id ?? '#',
        }),
        values,
    );
});

// define seat arrays using refs
const leftseats = computed(() => props.room.computers?.slice(0, 24) || []);
const rightseats = computed(() => props.room.computers?.slice(24, 48) || []);
</script>

<template>
    <div class="relative flex min-h-[calc(100vh-5rem)] overflow-hidden">
        <div
            :class="[
                'grid grid-cols-2 place-items-center transition-all duration-700',
                selectedComputers.length > 0 ? 'w-3/4' : 'w-full',
            ]"
        >
            <!-- row 1 -->
            <div class="grid grid-cols-4 grid-rows-6 place-items-center gap-4">
                <Computer
                    v-for="(computer, index) in leftseats"
                    :index="index + 1"
                    :key="computer.id"
                    :computer="computer"
                    :isSelected="selectedComputers.includes(computer.id)"
                    @click="handleClick(computer.id, $event)"
                />
            </div>

            <!-- row 2 -->
            <div class="grid grid-cols-4 grid-rows-6 place-items-center gap-4">
                <Computer
                    v-for="(computer, index) in rightseats"
                    :key="computer.id"
                    :index="index + 25"
                    :computer="computer"
                    :isSelected="selectedComputers.includes(computer.id)"
                    @click="handleClick(computer.id, $event)"
                />
            </div>
        </div>
        <div
            :class="[
                'bg-white-200 absolute right-0 top-0 h-full w-1/4 border-l-2',
                'max-h-[calc(100vh-5rem)] overflow-y-auto',
                'transform',
                // Use different transition timings based on panel state
                selectedComputers.length > 0
                    ? 'translate-x-0 transition-transform duration-700 ease-out'
                    : 'translate-x-full transition-transform duration-300 ease-in',
            ]"
        >
            <!-- Khu vực chi tiết và điều khiển -->
            <div class="flex flex-1 flex-col">
                <!-- Chi tiết máy -->
                <div class="border-b-2 p-4">
                    <h2 class="mb-4 text-lg font-semibold text-gray-800">
                        Thông tin máy
                    </h2>
                    <p><strong>Máy:</strong> {{ selectedComputer?.name }}</p>
                    <p><strong>IP:</strong> 192.168.1.1</p>
                    <p>
                        <strong>Trạng thái:</strong>
                        {{ selectedComputer?.status }}
                    </p>
                    <div class="stats my-4 space-y-2">
                        <p>
                            <strong>CPU:</strong>
                            <progress
                                class="h-3 w-full"
                                value="45"
                                max="100"
                            ></progress>
                            45%
                        </p>
                        <p>
                            <strong>RAM:</strong>
                            <progress
                                class="h-3 w-full"
                                value="60"
                                max="100"
                            ></progress>
                            60%
                        </p>
                        <p>
                            <strong>Disk:</strong>
                            <progress
                                class="h-3 w-full"
                                value="20"
                                max="100"
                            ></progress>
                            20%
                        </p>
                    </div>
                    <div class="applications">
                        <p class="font-medium">Ứng dụng:</p>
                        <ul class="mt-2 space-y-2">
                            <li class="flex items-center justify-between">
                                Chrome
                                <Button variant="destructive"> Tắt </Button>
                            </li>
                            <li class="flex items-center justify-between">
                                Word
                                <Button variant="destructive"> Tắt </Button>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Điều khiển lệnh -->
                <div class="p-4">
                    <h2 class="mb-4 text-lg font-semibold text-gray-800">
                        Điều khiển
                    </h2>
                    <div class="space-x-2 space-y-2">
                        <Button as-child>
                            <a href="#!">Khởi động lại</a>
                        </Button>
                        <Button as-child>
                            <a href="#!">Tắt máy</a>
                        </Button>
                        <Button as-child>
                            <a href="#!">Khóa máy</a>
                        </Button>
                        <Button as-child>
                            <a href="#!">Mở ứng dụng</a>
                        </Button>
                    </div>
                    <form
                        class="mt-4 w-full space-y-6"
                        @submit.prevent="onSubmit()"
                    >
                        <FormField v-slot="{ componentField }" name="command">
                            <FormItem>
                                <FormLabel>Control Command</FormLabel>
                                <FormControl>
                                    <Textarea
                                        placeholder="Tell us a little bit about yourself"
                                        class="resize-none"
                                        v-bind="componentField"
                                    />
                                </FormControl>
                                <FormDescription>
                                    You can <span>@mention</span> other users
                                    and organizations.
                                </FormDescription>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                        <Button type="submit"> Submit </Button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
