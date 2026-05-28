<script setup lang="ts">
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { Dialog, DialogContent } from '@/components/ui/dialog';
import { Form } from '@inertiajs/vue3';
import { AlertTriangle, X } from 'lucide-vue-next';
import { ref, useTemplateRef } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';

const passwordInput = useTemplateRef('passwordInput');
const open = ref(false);
</script>

<template>
    <div class="space-y-6">
        <HeadingSmall
            title="Delete account"
            description="Delete your account and all of its resources"
        />
        <div
            class="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10"
        >
            <div class="space-y-0.5 text-red-600 dark:text-red-100">
                <p class="text-sm">
                    Please proceed with caution, this cannot be undone.
                </p>
            </div>
            <button
                type="button"
                class="cursor-pointer rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-red-700"
                data-test="delete-user-button"
                @click="open = true"
            >
                Delete account
            </button>
        </div>
    </div>

    <Dialog v-model:open="open">
        <DialogContent
            class="overflow-hidden sm:max-w-sm"
            @open-auto-focus="(e) => e.preventDefault()"
        >
            <Form
                v-bind="ProfileController.destroy.form()"
                reset-on-success
                @error="() => passwordInput?.$el?.focus()"
                :options="{ preserveScroll: true }"
                v-slot="{ errors, processing, reset, clearErrors }"
            >
                <!-- Header -->
                <div
                    class="border-b border-black/[0.06] px-6 py-5 dark:border-white/[0.06]"
                >
                    <div class="flex items-center gap-3">
                        <span
                            class="flex size-8 items-center justify-center rounded-lg bg-red-50 dark:bg-red-500/10"
                        >
                            <AlertTriangle class="size-4 text-red-500" />
                        </span>
                        <div>
                            <p class="text-base font-semibold text-foreground">
                                Are you sure?
                            </p>
                            <p class="text-[13px] text-muted-foreground">
                                This action cannot be undone. This will
                                permanently delete it from our servers.
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="absolute top-4 right-4 flex size-7 cursor-pointer items-center justify-center rounded-lg text-muted-foreground/60 transition-colors hover:bg-black/[0.06] hover:text-foreground dark:hover:bg-white/[0.06]"
                        @click="
                            open = false;
                            clearErrors();
                            reset();
                        "
                    >
                        <X class="size-4" />
                    </button>
                </div>

                <!-- Body -->
                <div class="px-6 py-5">
                    <div class="space-y-1.5">
                        <label
                            class="text-[12px] font-semibold tracking-wide text-muted-foreground uppercase"
                        >
                            Password
                        </label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            ref="passwordInput"
                            placeholder="Enter your password"
                            class="w-full rounded-lg border border-black/[0.1] bg-[#fafafa] px-3 py-2 text-[14px] text-foreground transition-colors outline-none placeholder:text-muted-foreground/50 focus:border-black/30 focus:bg-white dark:border-white/[0.1] dark:bg-white/[0.04] dark:focus:border-white/30 dark:focus:bg-white/[0.06]"
                        />
                        <InputError :message="errors.password" />
                    </div>
                </div>

                <!-- Footer -->
                <div
                    class="flex items-center justify-end gap-2 border-t border-black/[0.06] px-6 py-4 dark:border-white/[0.06]"
                >
                    <button
                        type="button"
                        class="cursor-pointer rounded-lg px-4 py-2 text-sm font-medium text-muted-foreground transition-colors hover:bg-black/[0.05] disabled:opacity-50 dark:hover:bg-white/[0.05]"
                        :disabled="processing"
                        @click="
                            open = false;
                            clearErrors();
                            reset();
                        "
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="cursor-pointer rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-red-700 disabled:opacity-40"
                        :disabled="processing"
                        data-test="confirm-delete-user-button"
                    >
                        {{ processing ? 'Deleting...' : 'Delete' }}
                    </button>
                </div>
            </Form>
        </DialogContent>
    </Dialog>
</template>
