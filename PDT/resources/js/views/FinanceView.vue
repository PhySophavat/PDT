<template>
  <div class="space-y-6">
    <!-- Header -->
    <PageHeader 
      :title="$t('finance.title')" 
      :breadcrumb="$t('nav.feesCollection')" 
      :actionLabel="$t('finance.createInvoice')"
      @action="openInvoiceModal"
    />

    <!-- Sub Tabs -->
    <TabNav 
      v-model="activeTab" 
      :tabs="[
        { id: 'invoices', label: $t('finance.invoices') },
        { id: 'payments', label: $t('finance.payments') },
        { id: 'expenses', label: $t('finance.expenses') }
      ]"
    />

    <!-- 4 Statistics Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <StatCard :title="$t('dashboard.totalRevenue')" :value="formatCurrency(stats.revenue)" />
      <StatCard :title="$t('finance.invoices')" :value="formatCurrency(stats.invoiced)" />
      <StatCard :title="$t('dashboard.outstanding')" :value="formatCurrency(Math.max(0, stats.invoiced - stats.revenue))" />
      <StatCard :title="$t('dashboard.totalExpenses')" :value="formatCurrency(stats.expenses)" />
    </div>

    <!-- TAB 1: Invoices List -->
    <div v-if="activeTab === 'invoices'" class="space-y-4">
      <FilterBar>
        <template #left>
          <div class="flex items-center gap-3">
            <input 
              v-model="searchQuery"
              type="text"
              placeholder="ស្វែងរកតាមលេខវិក្កយបត្រ ឬឈ្មោះសិស្ស..."
              class="px-3.5 py-1.5 bg-white border border-slate-200 rounded-xl text-xs w-64 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
            />
          </div>
        </template>

        <template #right>
          <div class="inline-flex items-center gap-3 px-3.5 py-1.5 bg-slate-50 border border-slate-200/80 rounded-xl text-xs">
            <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
              <input type="radio" v-model="statusFilter" value="all" class="text-blue-600 focus:ring-0">
              <span>{{ $t('common.all') }}</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
              <input type="radio" v-model="statusFilter" value="paid" class="text-emerald-600 focus:ring-0">
              <span>បានបង់រួច</span>
            </label>
            <label class="flex items-center gap-1.5 cursor-pointer text-slate-600">
              <input type="radio" v-model="statusFilter" value="unpaid" class="text-rose-600 focus:ring-0">
              <span>មិនទាន់បង់</span>
            </label>
          </div>
        </template>
      </FilterBar>

      <DataTable>
        <template #header>
          <tr>
            <th class="py-3 px-4 font-bold text-slate-700">{{ $t('finance.invoiceNo') }}</th>
            <th class="py-3 px-4 font-bold text-slate-700">{{ $t('students.studentName') }}</th>
            <th class="py-3 px-4 font-bold text-slate-700">{{ $t('finance.titleDescription') }}</th>
            <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ $t('finance.totalAmount') }}</th>
            <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ $t('finance.paidAmount') }}</th>
            <th class="py-3 px-4 font-bold text-slate-700 text-center">{{ $t('common.status') }}</th>
            <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ $t('common.actions') }}</th>
          </tr>
        </template>

        <template #body>
          <tr v-if="filteredInvoices.length === 0">
            <td colspan="7" class="py-10 text-center text-slate-400">{{ $t('common.noData') }}</td>
          </tr>
          <tr v-for="inv in filteredInvoices" :key="inv.id" class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3 px-4 font-mono font-bold text-blue-600">{{ inv.invoice_number }}</td>
            <td class="py-3 px-4 font-bold text-slate-800">{{ inv.student }}</td>
            <td class="py-3 px-4 text-slate-600">{{ inv.title }}</td>
            <td class="py-3 px-4 text-right font-medium text-slate-800">${{ inv.total.toFixed(2) }}</td>
            <td class="py-3 px-4 text-right font-bold text-emerald-600">${{ (inv.paid || 0).toFixed(2) }}</td>
            <td class="py-3 px-4 text-center">
              <span :class="[
                'px-2.5 py-0.5 rounded-full text-[11px] font-bold',
                inv.status === 'paid' ? 'bg-emerald-100 text-emerald-700' :
                inv.status === 'partial' ? 'bg-amber-100 text-amber-700' :
                'bg-rose-100 text-rose-700'
              ]">
                {{ inv.status === 'paid' ? 'បានបង់រួច' : (inv.status === 'partial' ? 'បង់ខ្លះ' : 'មិនទាន់បង់') }}
              </span>
            </td>
            <td class="py-3 px-4 text-right space-x-1.5 whitespace-nowrap">
              <button 
                v-if="inv.status !== 'paid'"
                @click="openPaymentModal(inv)" 
                class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold hover:bg-emerald-100 transition"
              >
                + បង់ប្រាក់
              </button>
              <span v-else class="text-xs text-emerald-600 font-semibold px-2">✓ រួចរាល់</span>
            </td>
          </tr>
        </template>
      </DataTable>
    </div>

    <!-- TAB 2: Payments Log -->
    <div v-else-if="activeTab === 'payments'" class="space-y-4">
      <DataTable>
        <template #header>
          <tr>
            <th class="py-3 px-4 font-bold text-slate-700">លេខបង្កាន់ដៃ (Receipt No.)</th>
            <th class="py-3 px-4 font-bold text-slate-700">លេខវិក្កយបត្រ</th>
            <th class="py-3 px-4 font-bold text-slate-700">{{ $t('students.studentName') }}</th>
            <th class="py-3 px-4 font-bold text-slate-700">វិធីសាស្ត្រទូទាត់</th>
            <th class="py-3 px-4 font-bold text-slate-700">កាលបរិច្ឆេទ</th>
            <th class="py-3 px-4 font-bold text-slate-700 text-right">ចំនួនទឹកប្រាក់</th>
            <th class="py-3 px-4 font-bold text-slate-700 text-right">{{ $t('common.actions') }}</th>
          </tr>
        </template>

        <template #body>
          <tr v-if="paymentsList.length === 0">
            <td colspan="7" class="py-10 text-center text-slate-400">{{ $t('common.noData') }}</td>
          </tr>
          <tr v-for="pay in paymentsList" :key="pay.id" class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3 px-4 font-mono font-bold text-emerald-600">{{ pay.receipt_number }}</td>
            <td class="py-3 px-4 font-mono text-slate-600 text-xs">{{ pay.invoice_number }}</td>
            <td class="py-3 px-4 font-bold text-slate-800">{{ pay.student }}</td>
            <td class="py-3 px-4">
              <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 uppercase">
                {{ pay.payment_method }}
              </span>
            </td>
            <td class="py-3 px-4 text-slate-600 text-xs">{{ pay.payment_date }}</td>
            <td class="py-3 px-4 text-right font-bold text-slate-900">${{ pay.amount.toFixed(2) }}</td>
            <td class="py-3 px-4 text-right">
              <a 
                :href="`/finance/payments/${pay.id}/receipt`" 
                target="_blank" 
                class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition inline-block"
              >
                🖨️ បង្កាន់ដៃ
              </a>
            </td>
          </tr>
        </template>
      </DataTable>
    </div>

    <!-- TAB 3: Expenses Directory -->
    <div v-else-if="activeTab === 'expenses'" class="space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="font-bold text-slate-800 text-sm">កំណត់ត្រាចំណាយរបស់សាលា</h3>
        <button @click="showExpenseModal = true" class="px-3.5 py-1.5 bg-rose-600 text-white rounded-xl text-xs font-bold hover:bg-rose-700 transition shadow-sm">
          + កត់ត្រាចំណាយថ្មី
        </button>
      </div>

      <DataTable>
        <template #header>
          <tr>
            <th class="py-3 px-4 font-bold text-slate-700">ចំណងជើងចំណាយ</th>
            <th class="py-3 px-4 font-bold text-slate-700">ប្រភេទចំណាយ</th>
            <th class="py-3 px-4 font-bold text-slate-700">កាលបរិច្ឆេទ</th>
            <th class="py-3 px-4 font-bold text-slate-700 text-right">ចំនួនទឹកប្រាក់</th>
          </tr>
        </template>

        <template #body>
          <tr v-if="expensesList.length === 0">
            <td colspan="4" class="py-10 text-center text-slate-400">{{ $t('common.noData') }}</td>
          </tr>
          <tr v-for="exp in expensesList" :key="exp.id" class="hover:bg-slate-50/70 transition-colors">
            <td class="py-3 px-4 font-bold text-slate-800">{{ exp.title }}</td>
            <td class="py-3 px-4">
              <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-100">
                {{ exp.category }}
              </span>
            </td>
            <td class="py-3 px-4 text-slate-600 text-xs">{{ exp.date }}</td>
            <td class="py-3 px-4 text-right font-bold text-rose-600">-${{ exp.amount.toFixed(2) }}</td>
          </tr>
        </template>
      </DataTable>
    </div>

    <!-- Modal 1: Create Invoice Modal -->
    <div v-if="showInvoiceModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
            + បង្កើតវិក្កយបត្រថ្មី (New Invoice)
          </h3>
          <button @click="showInvoiceModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form @submit.prevent="submitCreateInvoice" class="p-6 space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ជ្រើសរើសសិស្ស *</label>
            <select v-model="invoiceForm.student_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
              <option value="">-- សូមជ្រើសរើសសិស្ស --</option>
              <option v-for="s in studentsList" :key="s.id" :value="s.id">
                {{ s.name }} ({{ s.student_id_card }}) - ថ្នាក់ {{ s.grade }}
              </option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ប្រភេទកម្រៃ</label>
              <select v-model="invoiceForm.fee_category_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option :value="null">-- ទូទៅ / ថ្លៃសិក្សា --</option>
                <option v-for="cat in feeCategoriesList" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ចំនួនទឹកប្រាក់ ($ USD) *</label>
              <input v-model.number="invoiceForm.total_amount" type="number" min="0.01" step="0.01" required placeholder="0.00" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ចំណងជើងវិក្កយបត្រ *</label>
            <input v-model="invoiceForm.title" type="text" required placeholder="ឧ. ថ្លៃសិក្សា ឆមាសទី ១" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ថ្ងៃផុតកំណត់បង់ប្រាក់</label>
            <input v-model="invoiceForm.due_date" type="date" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <button type="button" @click="showInvoiceModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200">
              បោះបង់
            </button>
            <button type="submit" :disabled="isSubmitting" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition disabled:opacity-50">
              <span v-if="isSubmitting">កំពុងចេញវិក្កយបត្រ...</span>
              <span v-else>ចេញវិក្កយបត្រ</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal 2: Record Payment Modal -->
    <div v-if="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
            កត់ត្រាការបង់ប្រាក់ (Record Payment)
          </h3>
          <button @click="showPaymentModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form @submit.prevent="submitRecordPayment" class="p-6 space-y-4">
          <div class="p-3 bg-blue-50 border border-blue-200/80 rounded-xl text-xs text-blue-900">
            <p><strong>លេខវិក្កយបត្រ:</strong> {{ selectedInvoiceForPayment?.invoice_number }}</p>
            <p><strong>សិស្ស:</strong> {{ selectedInvoiceForPayment?.student }}</p>
            <p><strong>ចំនួនសរុប:</strong> ${{ selectedInvoiceForPayment?.total.toFixed(2) }} (នៅខ្វះ: ${{ (selectedInvoiceForPayment?.total - selectedInvoiceForPayment?.paid).toFixed(2) }})</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ចំនួនទឹកប្រាក់បង់ ($ USD) *</label>
              <input v-model.number="paymentForm.amount" type="number" min="0.01" step="0.01" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">វិធីសាស្ត្រទូទាត់ *</label>
              <select v-model="paymentForm.payment_method" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="cash">សាច់ប្រាក់ (Cash)</option>
                <option value="qr_code">KHQR / Bakong</option>
                <option value="bank_transfer">ផ្ទេរតាមធនាគារ</option>
                <option value="other">ផ្សេងៗ</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">កាលបរិច្ឆេទបង់ប្រាក់ *</label>
            <input v-model="paymentForm.payment_date" type="date" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">កំណត់សម្គាល់ (Note)</label>
            <input v-model="paymentForm.note" type="text" placeholder="កំណត់សម្គាល់បន្ថែម..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <button type="button" @click="showPaymentModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200">
              បោះបង់
            </button>
            <button type="submit" :disabled="isSubmitting" class="px-5 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 transition disabled:opacity-50">
              <span v-if="isSubmitting">កំពុងកត់ត្រា...</span>
              <span v-else>កត់ត្រាការបង់ប្រាក់</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal 3: Record Expense Modal -->
    <div v-if="showExpenseModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
          <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-rose-600"></span>
            + កត់ត្រាចំណាយសាលា (Record Expense)
          </h3>
          <button @click="showExpenseModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <form @submit.prevent="submitRecordExpense" class="p-6 space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">ចំណងជើងចំណាយ *</label>
            <input v-model="expenseForm.title" type="text" required placeholder="ឧ. បង់ថ្លៃអគ្គិសនី និងទឹកខែមករា" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ប្រភេទចំណាយ *</label>
              <select v-model="expenseForm.category" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <option value="ទឹក-ភ្លើង">ទឹក-ភ្លើង (Utilities)</option>
                <option value="សម្ភារៈការិយាល័យ">សម្ភារៈការិយាល័យ</option>
                <option value="ជួសជុល-ថែទាំ">ជួសជុល-ថែទាំ</option>
                <option value="សកម្មភាពសាលា">សកម្មភាពសាលា</option>
                <option value="ផ្សេងៗ">ផ្សេងៗ</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">ចំនួនទឹកប្រាក់ ($ USD) *</label>
              <input v-model.number="expenseForm.amount" type="number" min="0.01" step="0.01" required placeholder="0.00" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-rose-600 focus:outline-none focus:ring-2 focus:ring-rose-500/20">
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">កាលបរិច្ឆេទចំណាយ *</label>
            <input v-model="expenseForm.expense_date" type="date" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20">
          </div>

          <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
            <button type="button" @click="showExpenseModal = false" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200">
              បោះបង់
            </button>
            <button type="submit" :disabled="isSubmitting" class="px-5 py-2 bg-rose-600 text-white rounded-xl text-xs font-bold hover:bg-rose-700 transition disabled:opacity-50">
              <span v-if="isSubmitting">កំពុងកត់ត្រា...</span>
              <span v-else>រក្សាទុកចំណាយ</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import axios from 'axios';
import PageHeader from '../components/PageHeader.vue';
import TabNav from '../components/TabNav.vue';
import StatCard from '../components/StatCard.vue';
import FilterBar from '../components/FilterBar.vue';
import DataTable from '../components/DataTable.vue';

const { t } = useI18n();

const activeTab = ref('invoices');
const statusFilter = ref('all');
const searchQuery = ref('');

const stats = ref({
  revenue: 0,
  invoiced: 0,
  expenses: 0,
});

const invoicesList = ref([]);
const paymentsList = ref([]);
const expensesList = ref([]);
const studentsList = ref([]);
const feeCategoriesList = ref([]);

const showInvoiceModal = ref(false);
const showPaymentModal = ref(false);
const showExpenseModal = ref(false);
const selectedInvoiceForPayment = ref(null);
const isSubmitting = ref(false);

const invoiceForm = ref({
  student_id: '',
  fee_category_id: null,
  title: 'ថ្លៃសិក្សា ឆមាសទី ១',
  total_amount: 250.00,
  due_date: new Date(Date.now() + 30 * 86400000).toISOString().slice(0, 10),
});

const paymentForm = ref({
  invoice_id: '',
  amount: 0,
  payment_method: 'cash',
  payment_date: new Date().toISOString().slice(0, 10),
  note: '',
});

const expenseForm = ref({
  title: '',
  category: 'ទឹក-ភ្លើង',
  amount: 0,
  expense_date: new Date().toISOString().slice(0, 10),
});

const fetchFinanceData = async () => {
  try {
    const res = await axios.get('/api/finance');
    stats.value = res.data.stats || { revenue: 0, invoiced: 0, expenses: 0 };
    invoicesList.value = res.data.invoices || [];
    paymentsList.value = res.data.payments || [];
    expensesList.value = res.data.expenses || [];
    studentsList.value = res.data.students || [];
    feeCategoriesList.value = res.data.feeCategories || [];
  } catch (err) {
    console.error('Failed to fetch finance:', err);
  }
};

onMounted(() => {
  fetchFinanceData();
});

const filteredInvoices = computed(() => {
  return invoicesList.value.filter(inv => {
    const matchesSearch = !searchQuery.value || 
      inv.invoice_number.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      inv.student.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      inv.title.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchesStatus = statusFilter.value === 'all' || inv.status === statusFilter.value;
    return matchesSearch && matchesStatus;
  });
});

const formatCurrency = (val) => {
  return '$' + Number(val || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const openInvoiceModal = () => {
  invoiceForm.value = {
    student_id: studentsList.value[0]?.id || '',
    fee_category_id: feeCategoriesList.value[0]?.id || null,
    title: 'ថ្លៃសិក្សា ឆមាសទី ១',
    total_amount: 250.00,
    due_date: new Date(Date.now() + 30 * 86400000).toISOString().slice(0, 10),
  };
  showInvoiceModal.value = true;
};

const openPaymentModal = (inv) => {
  selectedInvoiceForPayment.value = inv;
  const remaining = Math.max(0, inv.total - (inv.paid || 0));
  paymentForm.value = {
    invoice_id: inv.id,
    amount: remaining,
    payment_method: 'cash',
    payment_date: new Date().toISOString().slice(0, 10),
    note: '',
  };
  showPaymentModal.value = true;
};

const submitCreateInvoice = async () => {
  isSubmitting.value = true;
  try {
    const res = await axios.post('/api/finance/invoices', invoiceForm.value);
    showInvoiceModal.value = false;
    alert(res.data.message || 'បានចេញវិក្កយបត្រដោយជោគជ័យ!');
    await fetchFinanceData();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការចេញវិក្កយបត្រ!');
  } finally {
    isSubmitting.value = false;
  }
};

const submitRecordPayment = async () => {
  isSubmitting.value = true;
  try {
    const res = await axios.post('/api/finance/payments', paymentForm.value);
    showPaymentModal.value = false;
    alert(res.data.message || 'បានកត់ត្រាការបង់ប្រាក់ដោយជោគជ័យ!');
    await fetchFinanceData();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការកត់ត្រាការបង់ប្រាក់!');
  } finally {
    isSubmitting.value = false;
  }
};

const submitRecordExpense = async () => {
  isSubmitting.value = true;
  try {
    const res = await axios.post('/api/finance/expenses', expenseForm.value);
    showExpenseModal.value = false;
    alert(res.data.message || 'បានកត់ត្រាចំណាយដោយជោគជ័យ!');
    await fetchFinanceData();
  } catch (err) {
    alert(err.response?.data?.message || 'មានបញ្ហាក្នុងការកត់ត្រាចំណាយ!');
  } finally {
    isSubmitting.value = false;
  }
};

const handlePrint = () => window.print();

const handleExport = () => {
  const csv = [
    ['លេខវិក្កយបត្រ', 'សិស្ស', 'ចំណងជើង', 'ចំនួនសរុប', 'បានបង់', 'ស្ថានភាព'].join(','),
    ...invoicesList.value.map(inv => [
      `"${inv.invoice_number}"`,
      `"${inv.student}"`,
      `"${inv.title}"`,
      `"${inv.total}"`,
      `"${inv.paid || 0}"`,
      `"${inv.status}"`
    ].join(','))
  ].join('\n');

  const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `LMSTICK_Finance_${new Date().toISOString().slice(0, 10)}.csv`;
  a.click();
};
</script>
