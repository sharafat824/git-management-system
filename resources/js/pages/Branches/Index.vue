<template>
    <AppLayout>
        <AppContent class="p-6">
            <div class="space-y-6">
                <!-- Header Section -->
                <div class="space-y-4">
                    <Button variant="ghost" size="sm" @click="goBack" class="gap-2">
                        <ArrowLeft class="w-4 h-4" />
                        Back to repositories
                    </Button>

                    <Card>
                        <CardHeader>
                            <CardTitle class="text-2xl">{{ repository.name }}</CardTitle>
                            <CardDescription>{{ repository.full_name }}</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="flex items-center gap-4 text-sm text-muted-foreground">
                                <div class="flex items-center gap-2">
                                    <GitBranch class="w-4 h-4" />
                                    <span>Default: {{ repository.default_branch }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <GitFork class="w-4 h-4" />
                                    <span>{{ branches.length }} branches</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Branches Table -->
                <Card>
                    <CardHeader>
                        <CardTitle>Branches</CardTitle>
                        <CardDescription>
                            All branches synced from GitHub for this repository
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="p-0">
                        <div class="border-t">
                            <div class="relative overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b bg-muted/50">
                                            <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">
                                                Branch Name
                                            </th>
                                            <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">
                                                Created By
                                            </th>
                                            <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">
                                                Source
                                            </th>
                                            <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">
                                                Linked Task
                                            </th>
                                            <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">
                                                Created
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="branch in branches"
                                            :key="branch.id"
                                            class="border-b transition-colors hover:bg-muted/50"
                                        >
                                            <td class="p-6 align-middle">
                                                <div class="flex items-center gap-3">
                                                    <GitBranch class="w-4 h-4 text-muted-foreground" />
                                                    <code class="relative rounded bg-muted px-[0.3rem] py-[0.2rem] font-mono text-sm">
                                                        {{ branch.name }}
                                                    </code>
                                                </div>
                                            </td>
                                            <td class="p-6 align-middle">
                                                <span class="font-medium">{{ branch.created_by || 'N/A' }}</span>
                                            </td>
                                            <td class="p-6 align-middle">
                                                <code
                                                    v-if="branch.source_branch"
                                                    class="relative rounded bg-muted px-[0.3rem] py-[0.2rem] font-mono text-sm"
                                                >
                                                    {{ branch.source_branch }}
                                                </code>
                                                <span v-else class="text-muted-foreground">N/A</span>
                                            </td>
                                            <td class="p-6 align-middle">
                                                <Badge
                                                    v-if="branch.linked_task"
                                                    variant="secondary"
                                                >
                                                    {{ branch.linked_task }}
                                                </Badge>
                                                <span v-else class="text-muted-foreground">-</span>
                                            </td>
                                            <td class="p-6 align-middle text-muted-foreground">
                                                {{ formatDate(branch.created_at) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Empty State -->
                            <div
                                v-if="branches.length === 0"
                                class="text-center py-12"
                            >
                                <div class="mx-auto w-12 h-12 rounded-full bg-muted flex items-center justify-center mb-4">
                                    <GitBranch class="w-6 h-6 text-muted-foreground" />
                                </div>
                                <CardTitle class="text-lg mb-2">No branches yet</CardTitle>
                                <CardDescription>
                                    Branches will appear here once they're created on GitHub and synced to your system.
                                </CardDescription>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppContent>
    </AppLayout>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { ArrowLeft, GitBranch, GitFork } from 'lucide-vue-next'

// Import Wayfinder routes and layout
import AppLayout from '@/layouts/AppLayout.vue'
import AppContent from '@/components/AppContent.vue'
import repositories from '@/routes/repositories'

// Import shadcn components
import Button from '@/components/ui/button/Button.vue'
import Card from '@/components/ui/card/Card.vue'
import CardContent from '@/components/ui/card/CardContent.vue'
import CardDescription from '@/components/ui/card/CardDescription.vue'
import CardHeader from '@/components/ui/card/CardHeader.vue'
import CardTitle from '@/components/ui/card/CardTitle.vue'
import Badge from '@/components/ui/badge/Badge.vue'

const props = defineProps({
  repository: {
    type: Object,
    required: true
  },
  branches: {
    type: Array,
    default: () => []
  }
})

const goBack = () => {
  const repositoriesRoute = repositories.index()
  router.visit(repositoriesRoute.url)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>
