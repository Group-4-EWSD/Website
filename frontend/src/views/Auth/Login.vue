<script setup lang="ts">
import { ref } from "vue";
import { useForm, Field, ErrorMessage } from "vee-validate";
import * as yup from "yup";

const loading = ref(false);

const schema = yup.object({
  email: yup.string().email("Invalid email").required("Email is required"),
  password: yup.string().required("Password is required"),
});

interface loginForm{
    email: string,
    password: string
}

const { handleSubmit, errors } = useForm<loginForm>({
  validationSchema: schema,
});

const onSubmit = async (values: loginForm) => {
  loading.value = true;
  try {
    console.log("Login data:", values); 
  } catch (error) {
    console.error("Login failed:", error);
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md px-4">
      <div class="shadow-lg p-6 bg-white rounded-2xl">
        <h1 class="text-center text-2xl mb-4">Login</h1>
        <form @submit="handleSubmit(onSubmit)" class="space-y-4">
          <div class="space-y-2">
            <label for="email" class="block font-medium">Email</label>
            <Field
              id="email"
              name="email"
              type="email"
              placeholder="Enter your email"
              class="w-full p-2 border rounded"
            />
            <ErrorMessage name="email" class="text-sm text-red-500" />
          </div>
          <div class="space-y-2">
            <label for="password" class="block font-medium">Password</label>
            <Field
              id="password"
              name="password"
              type="password"
              placeholder="Enter your password"
              class="w-full p-2 border rounded"
            />
            <ErrorMessage name="password" class="text-sm text-red-500" />
          </div>
          <button
            type="submit"
            class="w-full py-2 bg-gray-700 text-white rounded"
            :disabled="loading"
          >
            {{ loading ? "Loading..." : "Login" }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>
