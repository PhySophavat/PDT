<template>
  <div class="space-y-6 font-khmer">
    <!-- Header -->
    <PageHeader 
      :title="t('nav.rolesPermissions')" 
      :breadcrumb="t('nav.rolesPermissions')" 
      :actionLabel="t('common.createNew')"
      @action="handleCreateNew"
    />

    <!-- Sub Tabs -->
    <TabNav 
      v-model="activeTab" 
      :tabs="[
        { id: 'all', label: t('common.all') },
        { id: 'admin', label: 'Admin' },
        { id: 'teacher', label: 'Teacher' },
        { id: 'student', label: 'Student' }
      ]"
    />

    <!-- Entity Banner -->
    <EntityBanner 
      :name="t('nav.rolesPermissions') + ' (System Access Control)'"
      subtitle="Security & User Role Management | PDT School System"
      metaId="SEC-ROLE-001"
      @print="handlePrint"
      @download="handleDownload"
    />

    <!-- 4 Statistics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard :title="t('nav.rolesPermissions')" :value="roles.length" />
      <StatCard title="Total Permissions" value="28" />
      <StatCard title="Active System Users" value="135" />
      <StatCard :title="t('common.status')" value="Protected" />
    </div>

    <!-- Filter Bar -->
    <FilterBar>
      <template #left>
        <span class="font-medium text-slate-700">Access Policy: <span class="font-bold text-[#1e40af]">Role-Based (RBAC)</span></span>
        <span class="font-medium text-slate-700 ml-2">Guard: <span class="font-bold text-emerald-600">Web Session</span></span>
      </template>

      <template #right>
        <div class="relative w-64">
          <input 
            type="text" 
            v-model="searchQuery" 
            :placeholder="t('common.searchPlaceholder')" 
            class="w-full px-3.5 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-xl text-xs placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
          />
        </div>
      </template>
    </FilterBar>

    <!-- Data Table -->
    <DataTable>
      <template #header>
        <tr>
          <th class="py-3 px-4 font-bold text-slate-700">Role ID</th>
          <th class="py-3 px-4 font-bold text-slate-700">Role Name</th>
          <th class="py-3 px-4 font-bold text-slate-700">Display Name</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">Assigned Users</th>
          <th class="py-3 px-4 font-bold text-slate-700">Key Permissions</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ t('common.status') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ t('common.actions') }}</th>
        </tr>
      </template>

      <template #body>
        <tr v-for="role in filteredRoles" :key="role.id" class="hover:bg-slate-50/70 transition-colors">
          <td class="py-3 px-4 font-mono font-bold text-blue-600">ROL-00{{ role.id }}</td>
          <td class="py-3 px-4 font-semibold text-slate-900 capitalize">{{ role.name }}</td>
          <td class="py-3 px-4 text-slate-700 font-medium">{{ role.displayName }}</td>
          <td class="py-3 px-4 text-center font-bold text-slate-800">{{ role.userCount }}</td>
          <td class="py-3 px-4 text-slate-500 text-xs">
            <span class="inline-block px-2 py-0.5 bg-slate-100 rounded text-slate-600 font-mono text-[11px] mr-1 mb-1" v-for="p in role.permissions" :key="p">
              {{ p }}
            </span>
          </td>
          <td class="py-3 px-4 text-center">
            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700">
              Active
            </span>
          </td>
          <td class="py-3 px-4 text-right">
            <button @click="handleEdit(role)" class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition">
              {{ t('common.edit') }}
            </button>
          </td>
        </tr>
      </template>
    </DataTable>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import PageHeader from '../components/PageHeader.vue';
import TabNav from '../components/TabNav.vue';
import EntityBanner from '../components/EntityBanner.vue';
import StatCard from '../components/StatCard.vue';
import FilterBar from '../components/FilterBar.vue';
import DataTable from '../components/DataTable.vue';

const { t } = useI18n();

const activeTab = ref('all');
const searchQuery = ref('');

const roles = ref([
  { id: 1, name: 'admin', displayName: 'អ្នកគ្រប់គ្រងប្រព័ន្ធ (Admin)', userCount: 2, permissions: ['* (All Access)', 'manage-users', 'finance-admin'] },
  { id: 2, name: 'teacher', displayName: 'លោកគ្រូ-អ្នកគ្រូ (Teacher)', userCount: 15, permissions: ['take-attendance', 'submit-scores', 'view-students'] },
  { id: 3, name: 'student', displayName: 'សិស្សានុសិស្ស (Student)', userCount: 110, permissions: ['view-attendance', 'view-scores', 'library-borrow'] },
  { id: 4, name: 'parent', displayName: 'មាតាបិតា-អាណាព្យាបាល (Parent)', userCount: 8, permissions: ['view-child-scores', 'view-invoices', 'child-attendance'] },
]);

const filteredRoles = computed(() => {
  return roles.value.filter(r => {
    if (activeTab.value !== 'all' && r.name !== activeTab.value) return false;
    if (!searchQuery.value) return true;
    const q = searchQuery.value.toLowerCase();
    return r.name.toLowerCase().includes(q) || r.displayName.toLowerCase().includes(q);
  });
});

const handleCreateNew = () => alert(t('common.createNew'));
const handlePrint = () => window.print();
const handleDownload = () => alert(t('common.download'));
const handleEdit = (role) => alert(`Editing permissions for: ${role.displayName}`);
</script>
