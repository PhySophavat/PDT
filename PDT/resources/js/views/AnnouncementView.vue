<template>
  <div class="space-y-6">
    <!-- Header -->
    <PageHeader 
      :title="$t('announcements.title')" 
      :breadcrumb="$t('nav.notice')" 
      :actionLabel="canManageAnnouncements ? $t('announcements.createNotice') : ''"
      @action="openCreateModal"
    />

    <!-- Sub Tabs -->
    <TabNav 
      v-model="activeAudience" 
      :tabs="[
        { id: 'all', label: $t('common.all') + ' ' + $t('announcements.title') },
        { id: 'teachers', label: $t('teachers.title') },
        { id: 'students', label: $t('students.title') },
        { id: 'parents', label: $t('parents.title') }
      ]"
    />

    <!-- 4 Statistics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard :title="$t('announcements.totalNotices')" :value="announcementsList.length" />
      <StatCard :title="$t('announcements.activeNotices')" :value="announcementsList.length" />
      <StatCard :title="$t('announcements.targetAudience')" :value="4" />
      <StatCard :title="$t('common.status')" :value="$t('common.active')" />
    </div>

    <!-- Filter Bar -->
    <FilterBar>
      <template #left>
        <input
          v-model="searchQuery"
          type="text"
          :placeholder="`${$t('common.search')} ${$t('announcements.title')}...`"
          class="px-3.5 py-1.5 bg-white border border-slate-200 rounded-xl text-xs w-64 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
        />
      </template>
      <template #right>
        <span class="text-xs text-slate-500">{{ $t('announcements.totalNotices') }}: <strong>{{ filteredAnnouncements.length }}</strong></span>
      </template>
    </FilterBar>

    <!-- Data Table -->
    <DataTable :loading="isLoading" :empty="filteredAnnouncements.length === 0" :colSpan="6" loadingText="កំពុងទាញយកសេចក្តីជូនដំណឹង...">
      <template #header>
        <tr>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('announcements.noticeTitle') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('announcements.category') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('announcements.targetAudience') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700">{{ $t('announcements.publishDate') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ $t('common.status') }}</th>
          <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ $t('common.actions') }}</th>
        </tr>
      </template>

      <template #body>
        <tr v-if="filteredAnnouncements.length === 0">
          <td colspan="6" class="py-10 text-center text-slate-400">{{ $t('common.noData') }}</td>
        </tr>
        <tr v-for="item in filteredAnnouncements" :key="item.id" class="hover:bg-slate-50/70 transition-colors">
          <td class="py-3 px-4 font-bold text-slate-900 max-w-sm">
            <span class="text-blue-700 block text-sm">{{ item.title }}</span>
            <span v-if="item.content" class="text-xs text-slate-500 line-clamp-1 font-normal">{{ item.content }}</span>
          </td>
          <td class="py-3 px-4 text-slate-600 font-medium">{{ item.category }}</td>
          <td class="py-3 px-4">
            <span :class="[
              'px-2.5 py-0.5 rounded-full text-[11px] font-semibold capitalize',
              item.target_audience === 'all' || item.target_audience === 'ទាំងអស់គ្នា' ? 'bg-blue-50 text-blue-700 border border-blue-100' :
              item.target_audience === 'teachers' || item.target_audience === 'គ្រូបង្រៀន' ? 'bg-violet-50 text-violet-700 border border-violet-100' :
              item.target_audience === 'students' || item.target_audience === 'សិស្ស' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' :
              'bg-amber-50 text-amber-700 border border-amber-100'
            ]">
              {{ item.target_audience === 'all' ? 'ទាំងអស់គ្នា' : (item.target_audience === 'teachers' ? 'គ្រូបង្រៀន' : (item.target_audience === 'students' ? 'សិស្សានុសិស្ស' : (item.target_audience === 'parents' ? 'អាណាព្យាបាល' : item.target_audience))) }}
            </span>
          </td>
          <td class="py-3 px-4 text-slate-600 text-xs font-mono">{{ item.date_formatted || item.publish_date }}</td>
          <td class="py-3 px-4 text-center">
            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
              {{ $t('common.active') }}
            </span>
          </td>
          <td class="py-3 px-4 text-right space-x-1.5 whitespace-nowrap">
            <button @click="viewNotice(item)" class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold hover:bg-slate-200 transition">
              អាន &rarr;
            </button>
            <button v-if="canManageAnnouncements" @click="handleDelete(item.id)" class="px-2 py-1 bg-rose-50 text-rose-600 rounded-lg text-xs font-semibold hover:bg-rose-100 transition">
              {{ $t('common.delete') }}
            </button>
          </td>
        </tr>
      </template>
    </DataTable>

    <!-- Create Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
            + បង្កើតសេចក្តីជូនដំណឹងថ្មី
          </h3>
          <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form @submit.prevent="submitNotice" class="p-6 space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ចំណងជើងសេចក្តីជូនដំណឹង *</label>
            <input v-model="newNotice.title" type="text" required placeholder="ឧ. កាលវិភាគប្រឡងឆមាសទី ១" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20" />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ប្រភេទដំណឹង</label>
              <select v-model="newNotice.category" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="ទូទៅ">ទូទៅ</option>
                <option value="ការប្រឡង">ការប្រឡង</option>
                <option value="កិច្ចប្រជុំ">កិច្ចប្រជុំ</option>
                <option value="កីឡា">កីឡា</option>
                <option value="ហិរញ្ញវត្ថុ">ហិរញ្ញវត្ថុ</option>
                <option value="ថ្ងៃឈប់សម្រាក">ថ្ងៃឈប់សម្រាក</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">គោលដៅអ្នកទទួល *</label>
              <select v-model="newNotice.target_audience" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="all">ទាំងអស់គ្នា</option>
                <option value="teachers">លោកគ្រូ-អ្នកគ្រូ</option>
                <option value="students">សិស្សានុសិស្ស</option>
                <option value="parents">អាណាព្យាបាល</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">កាលបរិច្ឆេទផ្សាយ</label>
            <input v-model="newNotice.publish_date" type="date" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20" />
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ខ្លឹមសារលម្អិតនៃសេចក្តីជូនដំណឹង *</label>
            <textarea v-model="newNotice.content" rows="4" required placeholder="សូមសរសេរខ្លឹមសារលម្អិតនៅទីនេះ..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20"></textarea>
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200">
              {{ $t('common.cancel') }}
            </button>
            <button type="submit" :disabled="isSubmitting" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition disabled:opacity-50">
              <span v-if="isSubmitting">កំពុងរក្សាទុក...</span>
              <span v-else>ផ្សាយសេចក្តីជូនដំណឹង</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- View Notice Detail Modal -->
    <div v-if="selectedNotice" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700">
            {{ selectedNotice.category }}
          </span>
          <button @click="selectedNotice = null" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>
        <div class="p-6 space-y-3">
          <h3 class="font-bold text-slate-900 text-base">{{ selectedNotice.title }}</h3>
          <p class="text-xs text-slate-400">ផ្សាយកាលពី: {{ selectedNotice.date_formatted || selectedNotice.publish_date }} | គោលដៅ: {{ selectedNotice.target_audience }}</p>
          <div class="pt-2 text-sm text-slate-700 whitespace-pre-line leading-relaxed">
            {{ selectedNotice.content || 'គ្មានព័ត៌មានបន្ថែម។' }}
          </div>
        </div>
        <div class="px-6 py-3 border-t border-slate-100 bg-slate-50 flex justify-end">
          <button @click="selectedNotice = null" class="px-4 py-1.5 bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold hover:bg-slate-300">
            បិទ
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onActivated } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import PageHeader from '../components/PageHeader.vue';
import TabNav from '../components/TabNav.vue';
import StatCard from '../components/StatCard.vue';
import FilterBar from '../components/FilterBar.vue';
import DataTable from '../components/DataTable.vue';

const { t } = useI18n();

const currentUser = ref(window.__INITIAL_USER__ || {});
const isStudent = computed(() => {
  const r = (currentUser.value?.role || '').toLowerCase();
  return !currentUser.value?.is_super_admin && !r.includes('admin') && !r.includes('teacher') && !r.includes('គ្រូ');
});
const canManageAnnouncements = computed(() => !isStudent.value);

const activeAudience = ref('all');
const searchQuery = ref('');
const showCreateModal = ref(false);
const selectedNotice = ref(null);
const isSubmitting = ref(false);

const announcementsList = ref([]);
const isLoading = ref(true);

// Hydrate from localStorage
try {
  const cached = localStorage.getItem('pdt_cached_announcements');
  if (cached) {
    const d = JSON.parse(cached);
    if (d.announcements && d.announcements.length) {
      announcementsList.value = d.announcements;
      isLoading.value = false;
    }
  }
} catch (e) {}

const newNotice = ref({
  title: '',
  category: 'ទូទៅ',
  target_audience: 'all',
  content: '',
  publish_date: new Date().toISOString().slice(0, 10),
});

const fetchAnnouncements = async () => {
  if (announcementsList.value.length === 0) {
    isLoading.value = true;
  }
  try {
    const res = await axios.get('/api/announcements', { params: { _t: Date.now() } });
    announcementsList.value = res.data.announcements || [];
    try {
      localStorage.setItem('pdt_cached_announcements', JSON.stringify(res.data));
    } catch (e) {}
  } catch (err) {
    console.error('Failed to fetch announcements:', err);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchAnnouncements();
});

onActivated(() => {
  fetchAnnouncements();
});

const filteredAnnouncements = computed(() => {
  return announcementsList.value.filter(a => {
    const matchesAudience = activeAudience.value === 'all' || 
      a.target_audience === activeAudience.value || 
      a.target_audience === 'all' ||
      a.target_audience === 'ទាំងអស់គ្នា';
    const q = searchQuery.value.toLowerCase();
    const matchesSearch = !q || 
      a.title.toLowerCase().includes(q) || 
      (a.category && a.category.toLowerCase().includes(q));
    return matchesAudience && matchesSearch;
  });
});

const openCreateModal = () => {
  newNotice.value = {
    title: '',
    category: 'ទូទៅ',
    target_audience: activeAudience.value !== 'all' ? activeAudience.value : 'all',
    content: '',
    publish_date: new Date().toISOString().slice(0, 10),
  };
  showCreateModal.value = true;
};

const viewNotice = (item) => {
  selectedNotice.value = item;
};

const submitNotice = async () => {
  if (!newNotice.value.title.trim()) return;
  isSubmitting.value = true;
  try {
    const res = await axios.post('/api/announcements', newNotice.value);
    showCreateModal.value = false;
    alert(res.data.message || 'បានផ្សាយសេចក្តីជូនដំណឹងជោគជ័យ!');
    await fetchAnnouncements();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការផ្សាយដំណឹង!');
  } finally {
    isSubmitting.value = false;
  }
};

const handleDelete = async (id) => {
  if (!confirm('តើអ្នកប្រាកដជាចង់លុបសេចក្តីជូនដំណឹងនេះមែនទេ?')) return;
  try {
    const res = await axios.delete(`/api/announcements/${id}`);
    alert(res.data.message || 'បានលុបសេចក្តីជូនដំណឹងដោយជោគជ័យ!');
    await fetchAnnouncements();
  } catch (err) {
    alert(err.response?.data?.message || 'មិនអាចលុបសេចក្តីជូនដំណឹងបានទេ!');
  }
};

const handlePrint = () => window.print();

const handleExport = () => {
  const csv = [
    ['ចំណងជើង', 'ប្រភេទ', 'គោលដៅ', 'កាលបរិច្ឆេទ'].join(','),
    ...announcementsList.value.map(a => [
      `"${a.title}"`,
      `"${a.category}"`,
      `"${a.target_audience}"`,
      `"${a.date_formatted || a.publish_date}"`
    ].join(','))
  ].join('\n');

  const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `Announcements_${new Date().toISOString().slice(0, 10)}.csv`;
  a.click();
};
</script>
