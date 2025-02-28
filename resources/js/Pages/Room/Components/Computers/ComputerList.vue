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
import { Room } from '@/types';
import { Link } from '@inertiajs/vue3';
import ComputerDialog from './ComputerDialog.vue';
import ComputerItem from './ComputerItem.vue';

const { toast } = useToast();

import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import { computed, h, reactive, ref } from 'vue';
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
        return props.room.machines.find((c) => c.id === selectedComputers[0]);
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

// Create grid representation
const gridCells = computed(() => {
    const cells = [];

    for (let row = 1; row <= props.room.grid_rows; row++) {
        const rowCells = [];
        for (let col = 1; col <= props.room.grid_cols; col++) {
            // Find if a computer exists at this position
            const computer = props.room.machines.find(
                (c) => c.pos_row === row && c.pos_col === col,
            );

            rowCells.push({
                row,
                col,
                computer,
                index: (row - 1) * props.room.grid_cols + col,
            });
        }
        cells.push(rowCells);
    }

    return cells;
});

// Add computer dialog state
const isComputerDialogOpen = ref(false);
const selectedPosition = ref({ row: 1, col: 1 });
const computerDialogFormId = 'add-computer-form';

const handleAddComputer = (row: number, col: number) => {
    selectedPosition.value = { row, col };
    isComputerDialogOpen.value = true;
};

const handleComputerSubmit = (data: any) => {
    router.post(route('computers.store'), data, {
        onSuccess: () => {
            isComputerDialogOpen.value = false;
            toast({
                title: 'Máy tính đã được thêm',
                description: `Máy ${data.name} đã được thêm thành công vào vị trí (${data.pos_row}, ${data.pos_col})`,
            });
        },
        onError: (errors) => {
            toast({
                title: 'Lỗi khi thêm máy tính',
                description: Object.values(errors).join(', '),
                variant: 'destructive',
            });
        },
    });
};

const closeComputerDialog = () => {
    isComputerDialogOpen.value = false;
};
</script>

<template>
    <!-- min-h-[calc(100vh-5rem)]  -->
    <div class="relative flex h-full overflow-hidden p-4">
        <div
            :class="[
                'transition-all duration-700',
                selectedComputers.length > 0 ? 'w-3/4' : 'w-full',
            ]"
        >
            <!-- Dynamic grid based on room dimensions -->
            <div class="flex h-full items-center justify-center">
                <div
                    class="grid grid-flow-row auto-rows-min gap-5"
                    :style="{
                        // gridTemplateRows: `repeat(${room.grid_rows}, minmax(0, 1fr))`,
                        gridTemplateColumns: `repeat(${room.grid_cols}, min-content)`,
                    }"
                >
                    <template v-for="row in gridCells" :key="row[0].row">
                        <template v-for="cell in row" :key="cell.index">
                            <!-- If computer exists at this position -->
                            <ComputerItem
                                v-if="cell.computer"
                                :key="`computer-${cell.row}-${cell.col}`"
                                :index="cell.index"
                                :machine="cell.computer"
                                :isSelected="
                                    selectedComputers.includes(cell.computer.id)
                                "
                                @click="handleClick(cell.computer.id, $event)"
                            />

                            <!-- Empty slot with + sign -->
                            <div
                                v-else
                                :key="`empty-${cell.row}-${cell.col}`"
                                class="flex h-14 w-14 cursor-pointer select-none items-center justify-center rounded border-2 border-dashed border-gray-300 text-2xl text-gray-400 hover:border-gray-500 hover:text-gray-600"
                                @click="handleAddComputer(cell.row, cell.col)"
                            >
                                +
                            </div>
                        </template>
                    </template>
                </div>
            </div>
        </div>
        <div
            :class="[
                'absolute right-0 top-0 h-full w-1/4 rounded-r-xl border-l-2 border-l-black bg-gray-300',
                'max-h-[100vh] overflow-y-auto',
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
                    <p>
                        <strong>IP:</strong> {{ selectedComputer?.ip_address }}
                    </p>
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
                        <Button
                            as-child
                            @click="
                                () => {
                                    toast({
                                        title: 'Scheduled: Catch up',
                                        description:
                                            'Monday, January 13, 2025 at 5:57 PM',
                                    });
                                }
                            "
                        >
                            <Link
                                :href="
                                    route('computers.command', {
                                        id: selectedComputer?.id ?? '#',
                                    })
                                "
                                method="post"
                                as="button"
                                :data="{ command: 'restart' }"
                            >
                                Khởi động lại
                            </Link>
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
                                        placeholder="Type your command here..."
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

        <!-- Add Computer Dialog -->
        <ComputerDialog
            :form-id="computerDialogFormId"
            :is-open="isComputerDialogOpen"
            :position="selectedPosition"
            :room-id="room.id"
            @update:is-open="isComputerDialogOpen = $event"
            @submit="handleComputerSubmit"
            @close="closeComputerDialog"
        />
    </div>
</template>

<style scoped></style>
