<template>
    <AppLayout>
        <AppContent class="p-6">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex justify-between items-center">
                    <div>
                        <CardTitle class="text-2xl">Git Repositories</CardTitle>
                        <CardDescription>Automatically synced from GitHub</CardDescription>
                    </div>
                    <Badge variant="secondary">
                        Last synced: {{ formatDate(lastSynced) }}
                    </Badge>
                </div>

                <!-- Repository Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Card
                        v-for="repo in repositoriesList"
                        :key="repo.id"
                        class="group cursor-pointer transition-all hover:shadow-md"
                        @click="navigateToBranches(repo.id)"
                    >
                        <CardHeader class="pb-3">
                            <div class="flex items-start justify-between">
                                <CardTitle class="text-base leading-6 truncate flex-1 mr-3">
                                    {{ repo.name }}
                                </CardTitle>
                                <Badge :variant="repo.private ? 'destructive' : 'default'" class="shrink-0">
                                    {{ repo.private ? 'Private' : 'Public' }}
                                </Badge>
                            </div>
                            <CardDescription class="line-clamp-2 mt-2">
                                {{ repo.description || 'No description provided' }}
                            </CardDescription>
                        </CardHeader>

                        <CardContent class="pb-3">
                            <div class="flex items-center justify-between text-sm text-muted-foreground">
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-1">
                                        <GitBranch class="w-4 h-4" />
                                        <span>{{ repo.default_branch }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <GitFork class="w-4 h-4" />
                                        <span>{{ repo.branches_count }} branches</span>
                                    </div>
                                </div>
                            </div>
                        </CardContent>

                        <CardFooter class="pt-0">
                            <Button variant="outline" class="w-full group-hover:bg-accent">
                                View Branches
                                <ArrowRight class="w-4 h-4 ml-2" />
                            </Button>
                        </CardFooter>
                    </Card>
                </div>

                <!-- Empty State -->
                <Card v-if="repositoriesList.length === 0" class="text-center">
                    <CardContent class="pt-6 pb-6">
                        <div class="mx-auto w-12 h-12 rounded-full bg-muted flex items-center justify-center mb-4">
                            <GitBranch class="w-6 h-6 text-muted-foreground" />
                        </div>
                        <CardTitle class="mb-2">No repositories yet</CardTitle>
                        <CardDescription class="mb-6">
                            Repositories will appear here once they're created on GitHub and synced to your system.
                        </CardDescription>
                        <Button as-child>
                            <a href="https://github.com/new" target="_blank" rel="noopener noreferrer">
                                Create Repository on GitHub
                                <ExternalLink class="w-4 h-4 ml-2" />
                            </a>
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </AppContent>
    </AppLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { GitBranch, GitFork, ArrowRight, ExternalLink } from 'lucide-vue-next'

// Import Wayfinder routes and layout
import AppLayout from '@/layouts/AppLayout.vue'
import AppContent from '@/components/AppContent.vue'
import repositories from '@/routes/repositories'

// Import shadcn components
import Button from '@/components/ui/button/Button.vue'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import CardDescription from '@/components/ui/card/CardDescription.vue'
import CardFooter from '@/components/ui/card/CardFooter.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import Badge from '@/components/ui/badge/Badge.vue'

const props = defineProps({
  repositoriesList: {
    type: Array,
    default: () => []
  },
  lastSynced: {
    type: String,
    default: () => new Date().toISOString()
  }
})

const navigateToBranches = (repositoryId) => {
  const branchRoute = repositories.branches(repositoryId)
  router.visit(branchRoute.url)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
