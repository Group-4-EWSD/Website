<script setup lang="ts">
import { useForm, Field } from "vee-validate";
import * as yup from "yup";
import { useRouter } from "vue-router";
import { register } from "@/api/auth";
import type { Credentials, RegisterData } from "@/types/auth";
import { ref } from "vue";

const router = useRouter();
const loading = ref(false);

const schema = yup.object({
  name: yup.string().required("Name is required"),
  email: yup.string().email("Invalid email").required("Email is required"),
  password: yup.string().min(6, "Password must be at least 6 characters").required("Password is required"),
  confirmPassword: yup
    .string()
    .oneOf([yup.ref("password")], "Passwords must match")
    .required("Confirm password is required"),
});

const { handleSubmit, errors } = useForm<RegisterData>({
  validationSchema: schema,
});

const onSubmit = handleSubmit(async (values: RegisterData) => {
  loading.value = true;
  try {
    await register(values);
    router.push("/login");
  } catch (error) {
    console.error("Registration failed:", error);
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-6 bg-white shadow-md rounded-lg">
      <h2 class="text-2xl font-bold text-center mb-4">Register</h2>
      <form @submit="onSubmit" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <Field name="name" type="text" class="w-full p-2 border rounded" />
          <p class="text-red-500 text-xs">{{ errors.name }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <Field name="email" type="email" class="w-full p-2 border rounded" />
          <p class="text-red-500 text-xs">{{ errors.email }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Password</label>
          <Field name="password" type="password" class="w-full p-2 border rounded" />
          <p class="text-red-500 text-xs">{{ errors.password }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
          <Field name="confirmPassword" type="password" class="w-full p-2 border rounded" />
          <p class="text-red-500 text-xs">{{ errors.confirmPassword }}</p>
        </div>

        <button 
          type="submit"
          class="w-full p-2 text-white bg-gray-700 rounded"
          :disabled="loading"
        >
          {{ loading ? "Registering..." : "Register" }}
        </button>
      </form>
    </div>
  </div>
</template>
