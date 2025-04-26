<script setup lang="ts">
import { ref, computed } from 'vue'
import { ArrowRightIcon, BookOpenIcon, ImageIcon, MessageSquareIcon } from 'lucide-vue-next'
import { useRouter } from 'vue-router'
import { useUserStore } from '@/stores/user'
import Logo from '@/assets/logo.png'

const router = useRouter()
const userStore = useUserStore()
const isHovering = ref(false)

// Determine user role for personalized content
const userRole = computed(() => userStore.currentUser?.user_type_name || '')

// Role-specific welcome content
const welcomeMessage = computed(() => {
  switch (userRole.value) {
    case 'Admin':
      return "Welcome to Aurora Magazine's Administrative Portal"
    case 'Marketing Manager':
      return "Welcome to Aurora University's Magazine Platform"
    case 'Marketing Coordinator':
      return `Welcome to Aurora Magazine's Faculty Portal`
    case 'Student':
      return "Welcome to Aurora University's Student Magazine"
    case 'Guest':
      return "Welcome to Aurora University's Magazine Showcase"
    default:
      return 'Welcome to Aurora University Magazine'
  }
})

// Role-specific descriptions
const welcomeDescription = computed(() => {
  switch (userRole.value) {
    case 'Admin':
      return "Manage system settings, user accounts, and closure dates for Aurora University's magazine contributions."
    case 'Marketing Manager':
      return 'Oversee all selected contributions across faculties and prepare content for the annual university magazine.'
    case 'Marketing Coordinator':
      return 'Review, comment on, and select student contributions from your faculty for the annual university magazine.'
    case 'Student':
      return 'Share your stories, articles, and photographs with the university community through our annual magazine.'
    case 'Guest':
      return 'Browse selected student contributions from faculties across Aurora University.'
    default:
      return "Aurora's platform for collecting, reviewing, and publishing student contributions for our annual magazine."
  }
})

// Role-specific features
const features = computed(() => {
  switch (userRole.value) {
    case 'Admin':
      return [
        {
          title: 'Manage Settings',
          description: 'Set closure dates, manage academic years, and configure system parameters.',
        },
        {
          title: 'User Administration',
          description:
            'Create and manage user accounts for coordinators, managers, and guest access.',
        },
        {
          title: 'Monitor Activity',
          description: 'View system statistics and generate reports across all faculties.',
        },
      ]
    case 'Marketing Manager':
      return [
        {
          title: 'Review Selections',
          description: 'Access all faculty-selected contributions ready for publication.',
        },
        {
          title: 'Download Content',
          description: 'Export selected contributions and images for magazine production.',
        },
        {
          title: 'Analyze Statistics',
          description: 'View participation metrics across all university faculties.',
        },
      ]
    case 'Marketing Coordinator':
      return [
        {
          title: 'Review Submissions',
          description: 'Review and comment on student contributions from your faculty.',
        },
        {
          title: 'Collaborate',
          description: 'Work with students to refine their content for potential publication.',
        },
        {
          title: 'Select Content',
          description: 'Choose the best contributions to represent your faculty in the magazine.',
        },
      ]
    case 'Student':
      return [
        {
          title: 'Submit Articles',
          description: 'Share your written work for consideration in the university magazine.',
        },
        {
          title: 'Upload Photos',
          description: 'Contribute high-quality images to showcase in the publication.',
        },
        {
          title: 'Receive Feedback',
          description:
            "Get comments from your faculty's Marketing Coordinator to improve your work.",
        },
      ]
    case 'Guest':
      return [
        {
          title: 'Browse Content',
          description: 'Explore selected contributions from students across the university.',
        },
        {
          title: 'View by Faculty',
          description: "Filter content by specific faculties you're interested in.",
        },
        {
          title: 'Discover Talent',
          description: 'See the creative works produced by Aurora University students.',
        },
      ]
    default:
      return [
        {
          title: 'Contribute',
          description: 'Submit articles and images for the university magazine.',
        },
        {
          title: 'Collaborate',
          description: 'Work with faculty coordinators to refine your submissions.',
        },
        {
          title: 'Connect',
          description: 'Join the creative community at Aurora University.',
        },
      ]
  }
})

// Role-specific button text
const buttonText = computed(() => {
  switch (userRole.value) {
    case 'Admin':
      return 'Go to Dashboard'
    case 'Marketing Manager':
      return 'View Contributions'
    case 'Marketing Coordinator':
      return 'Review Submissions'
    case 'Student':
      return 'Start Contributing'
    case 'Guest':
      return 'Browse Magazine'
    default:
      return 'Get Started'
  }
})

// Role-specific navigation destination
const handleStart = () => {
  router.push('/auth/login')
}
</script>

<template>
  <div
    class="flex flex-col items-center justify-center min-h-screen bg-gradient-to-br from-accent/5 via-background to-primary/10 p-4"
  >
    <div
      class="w-full max-w-3xl mx-auto flex flex-col items-center text-center"
    >
      <!-- Logo Section -->
      <div class="mb-8 w-full max-w-[200px]">
        <img :src="Logo" alt="Aurora University Magazine" class="w-full h-auto" />
      </div>

      <!-- Welcome Content -->
      <div class="space-y-6 mb-12">
        <h1 class="text-2xl lg:text-3xl font-bold text-primary">{{ welcomeMessage }}</h1>
        <p class="text-lg md:text-xl text-muted-foreground max-w-lg mx-auto">
          {{ welcomeDescription }}
        </p>
      </div>

      <!-- Features Section -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12 w-full">
        <div
          v-for="(feature, index) in features"
          :key="index"
          class="bg-card rounded-lg p-6 shadow-sm"
        >
          <div class="flex justify-center mb-4">
            <BookOpenIcon v-if="index === 0" class="h-8 w-8 text-primary" />
            <ImageIcon v-else-if="index === 1" class="h-8 w-8 text-primary" />
            <MessageSquareIcon v-else class="h-8 w-8 text-primary" />
          </div>
          <h3 class="font-semibold text-lg mb-2">{{ feature.title }}</h3>
          <p class="text-muted-foreground">{{ feature.description }}</p>
        </div>
      </div>

      <!-- Start Button -->
      <button
        @click="handleStart"
        @mouseenter="isHovering = true"
        @mouseleave="isHovering = false"
        class="group relative flex items-center gap-4 px-8 py-4 group bg-primary text-primary-foreground rounded-full font-medium text-lg transition-all duration-300 hover:bg-primary/90 hover:pl-12 hover:pr-10"
      >
        <span>{{ buttonText }}</span>
      </button>

      <!-- Footer with version info -->
      <div class="mt-16 text-sm text-muted-foreground">
        <p>Aurora University Magazine System v1.0.0</p>
      </div>
    </div>
  </div>
</template>
