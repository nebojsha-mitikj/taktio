<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
    <Head title="Log in" />

    <AuthBase
        title="Log in to your account"
        description="Enter your email and password below to log in"
        :nav-href="canRegister ? register.url() : undefined"
        :nav-label="canRegister ? 'Register' : undefined"
    >
        <!-- Status -->
        <div
            v-if="status"
            class="mb-4 text-center text-sm font-medium text-green-600"
        >
            {{ status }}
        </div>

        <!-- Form -->
        <Form
            v-bind="store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <!-- Email -->
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>

                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="email@example.com"
                        :tabindex="1"
                    />

                    <InputError :message="errors.email" />
                </div>

                <!-- Password -->
                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>

                        <TextLink
                            v-if="canResetPassword"
                            :href="request()"
                            class="text-sm"
                            :tabindex="4"
                        >
                            Forgot password?
                        </TextLink>
                    </div>

                    <Input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Password"
                        :tabindex="2"
                    />

                    <InputError :message="errors.password" />
                </div>

                <!-- Remember -->
                <Label
                    for="remember"
                    class="flex items-center space-x-3"
                >
                    <Checkbox
                        id="remember"
                        name="remember"
                        :tabindex="3"
                    />
                    <span>Remember me</span>
                </Label>

                <!-- Submit -->
                <Button
                    type="submit"
                    class="mt-4 w-full bg-brand text-page hover:bg-brand-hover"
                    :disabled="processing"
                    :tabindex="5"
                    data-test="login-button"
                >
                    <Spinner v-if="processing" class="mr-2" />
                    Log in
                </Button>
            </div>

            <!-- Footer -->
            <div
                v-if="canRegister"
                class="text-center text-sm text-muted-foreground"
            >
                Don't have an account?
                <TextLink
                    :href="register()"
                    :tabindex="6"
                >
                    Sign up
                </TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
