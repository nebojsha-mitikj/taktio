<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { Form, Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Register" />

    <AuthBase
        title="Create an account"
        description="Enter your details below to create your account"
        :nav-href="login.url()"
        nav-label="Log in"
    >
        <!-- Form -->
        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <div class="grid gap-6">
                <!-- Name -->
                <div class="grid gap-2">
                    <Label for="name">Name</Label>

                    <Input
                        id="name"
                        type="text"
                        name="name"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Full name"
                        :tabindex="1"
                    />

                    <InputError :message="errors.name" />
                </div>

                <!-- Email -->
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>

                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autocomplete="email"
                        placeholder="email@example.com"
                        :tabindex="2"
                    />

                    <InputError :message="errors.email" />
                </div>

                <!-- Password -->
                <div class="grid gap-2">
                    <Label for="password">Password</Label>

                    <Input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="Password"
                        :tabindex="3"
                    />

                    <InputError :message="errors.password" />
                </div>

                <!-- Confirm Password -->
                <div class="grid gap-2">
                    <Label for="password_confirmation">
                        Confirm password
                    </Label>

                    <Input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Confirm password"
                        :tabindex="4"
                    />

                    <InputError :message="errors.password_confirmation" />
                </div>

                <!-- Submit -->
                <Button
                    type="submit"
                    class="mt-2 w-full bg-brand text-page hover:bg-brand-hover"
                    :disabled="processing"
                    :tabindex="5"
                    data-test="register-user-button"
                >
                    <Spinner v-if="processing" class="mr-2" />
                    Create account
                </Button>
            </div>

            <!-- Footer -->
            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="login()" :tabindex="6"> Log in </TextLink>
            </div>
        </Form>
    </AuthBase>
</template>
