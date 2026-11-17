import { fromDate, constants } from '@thyrith/momentkh';

const KHMER_DIGITS = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];

export const toKhmerNumber = (num) => {
  if (num === null || num === undefined) return '';
  return String(num).replace(/[0-9]/g, (digit) => KHMER_DIGITS[parseInt(digit, 10)]);
};

export const SOLAR_MONTHS_KM = [
  'មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា',
  'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'
];

export const SOLAR_MONTHS_EN = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
];

export const WEEKDAYS_KM = [
  'អាទិត្យ', 'ចន្ទ', 'អង្គារ', 'ពុធ', 'ព្រហស្បតិ៍', 'សុក្រ', 'សៅរ៍'
];

export const WEEKDAYS_SHORT_KM = [
  'អាទិ', 'ចន្ទ', 'អង្គ', 'ពុធ', 'ព្រហ', 'សុក្រ', 'សៅរ៍'
];

export const ANIMAL_EMOJIS = {
  'ជូត': '🐀',
  'ឆ្លូវ': '🐂',
  'ខាល': '🐅',
  'ថោះ': '🐇',
  'រោង': '🐉',
  'ម្សាញ់': '🐍',
  'មមី': '🐎',
  'មមែ': '🐐',
  'វក': '🐒',
  'រកា': '🐓',
  'ច': '🐕',
  'កុរ': '🐖'
};

/**
 * Get comprehensive Khmer Lunar details for a given JS Date
 */
export const getKhmerLunarDetails = (jsDate) => {
  const info = fromDate(jsDate);
  const k = info.khmer;
  const g = info.gregorian;

  const khmerDayNum = toKhmerNumber(k.day);
  const khmerBeYear = toKhmerNumber(k.beYear);
  const khmerSolarDay = toKhmerNumber(g.day);
  const khmerSolarYear = toKhmerNumber(g.year);
  const solarMonthNameKm = SOLAR_MONTHS_KM[g.month - 1];

  // Determine if it is a Holy Day (ថ្ងៃសីល) or Shaving Day (ថ្ងៃកោរ)
  let isHolyDay = false;
  let isShavingDay = false;
  let holyDayLabel = '';
  let specialBadge = '';

  // Check next day to know if today is the last day of lunar month (ថ្ងៃដាច់ខែ)
  const tomorrow = new Date(jsDate.getTime() + 86400000);
  const tomorrowInfo = fromDate(tomorrow);
  const isLastDayOfLunarMonth = (tomorrowInfo.khmer.moonPhase === 0 && tomorrowInfo.khmer.day === 1);

  if (k.moonPhase === 0) {
    // Waxing (កើត)
    if (k.day === 8) {
      isHolyDay = true;
      holyDayLabel = 'ថ្ងៃសីល (៨ កើត)';
    } else if (k.day === 15) {
      isHolyDay = true;
      holyDayLabel = 'ថ្ងៃសីល (១៥ កើត ពេញបូណ៌មី)';
      specialBadge = 'ពេញបូណ៌មី';
    } else if (k.day === 7) {
      isShavingDay = true;
      holyDayLabel = 'ថ្ងៃកោរ (៧ កើត)';
    } else if (k.day === 14) {
      isShavingDay = true;
      holyDayLabel = 'ថ្ងៃកោរ (១៤ កើត)';
      specialBadge = 'ថ្ងៃកោរ';
    }
  } else {
    // Waning (រោច)
    if (k.day === 8) {
      isHolyDay = true;
      holyDayLabel = 'ថ្ងៃសីល (៨ រោច)';
    } else if (isLastDayOfLunarMonth || k.day === 15) {
      isHolyDay = true;
      holyDayLabel = 'ថ្ងៃសីល (ថ្ងៃដាច់ខែ)';
      specialBadge = 'ថ្ងៃដាច់ខែ';
    } else if (k.day === 7) {
      isShavingDay = true;
      holyDayLabel = 'ថ្ងៃកោរ (៧ រោច)';
    } else if (tomorrowInfo.khmer.moonPhase === 1 && tomorrowInfo.khmer.day === 14 && tomorrowInfo.isHolyDay) {
      isShavingDay = true;
      holyDayLabel = 'ថ្ងៃកោរ';
      specialBadge = 'ថ្ងៃកោរ';
    }
  }

  // Short label displayed inside calendar day cell:
  // E.g. "កត្តិក" if day 1 of month, otherwise "៨ កើត", "១៥ កើត", "១ រោច"
  let cellLunarText = `${khmerDayNum} ${k.moonPhaseName}`;
  if (k.day === 1 && k.moonPhase === 0) {
    cellLunarText = k.monthName;
  }

  // Full formal Khmer date string matching traditional format
  // Example: "ថ្ងៃអង្គារ ៨កើត ខែកត្តិក ឆ្នាំមមី អដ្ឋស័ក ពុទ្ធសករាជ ២៥៧០ ត្រូវនឹងថ្ងៃទី១៧ ខែវិច្ឆិកា ឆ្នាំ២០២៦"
  const formattedKhmerFull = `ថ្ងៃ${k.dayOfWeekName} ${khmerDayNum}${k.moonPhaseName} ខែ${k.monthName} ឆ្នាំ${k.animalYearName} ${k.sakName} ពុទ្ធសករាជ ${khmerBeYear} ត្រូវនឹងថ្ងៃទី${khmerSolarDay} ខែ${solarMonthNameKm} ឆ្នាំ${khmerSolarYear}`;

  const animalEmoji = ANIMAL_EMOJIS[k.animalYearName] || '🐎';

  return {
    raw: info,
    day: k.day,
    dayKm: khmerDayNum,
    moonPhase: k.moonPhase,
    moonPhaseName: k.moonPhaseName,
    monthIndex: k.monthIndex,
    monthName: k.monthName,
    beYear: k.beYear,
    beYearKm: khmerBeYear,
    animalYearName: k.animalYearName,
    animalEmoji,
    sakName: k.sakName,
    dayOfWeek: k.dayOfWeek,
    dayOfWeekName: k.dayOfWeekName,
    isHolyDay,
    isShavingDay,
    holyDayLabel,
    specialBadge,
    cellLunarText,
    formattedKhmerFull,
    solarDay: g.day,
    solarDayKm: khmerSolarDay,
    solarMonth: g.month,
    solarMonthNameKm,
    solarYear: g.year,
    solarYearKm: khmerSolarYear,
    zodiac: getWesternZodiac(g.month, g.day),
    fengShui: getDailyFengShui(k.animalYearName, k.dayOfWeek),
  };
};

export const ANIMAL_CONFLICT_MAP = {
  'ជូត': { conflictName: 'មមី', emoji: '🐎', note: 'ឆុងឆ្នាំ មមី' },
  'ឆ្លូវ': { conflictName: 'មមែ', emoji: '🐐', note: 'ឆុងឆ្នាំ មមែ' },
  'ខាល': { conflictName: 'វក', emoji: '🐒', note: 'ឆុងឆ្នាំ វក' },
  'ថោះ': { conflictName: 'រកា', emoji: '🐓', note: 'ឆុងឆ្នាំ រកា' },
  'រោង': { conflictName: 'ច', emoji: '🐕', note: 'ឆុងឆ្នាំ ច' },
  'ម្សាញ់': { conflictName: 'កុរ', emoji: '🐖', note: 'ឆុងឆ្នាំ កុរ' },
  'មមី': { conflictName: 'ជូត', emoji: '🐀', note: 'ឆុងឆ្នាំ ជូត' },
  'មមែ': { conflictName: 'ឆ្លូវ', emoji: '🐂', note: 'ឆុងឆ្នាំ ឆ្លូវ' },
  'វក': { conflictName: 'ខាល', emoji: '🐅', note: 'ឆុងឆ្នាំ ខាល' },
  'រកា': { conflictName: 'ថោះ', emoji: '🐇', note: 'ឆុងឆ្នាំ ថោះ' },
  'ច': { conflictName: 'រោង', emoji: '🐉', note: 'ឆុងឆ្នាំ រោង' },
  'កុរ': { conflictName: 'ម្សាញ់', emoji: '🐍', note: 'ឆុងឆ្នាំ ម្សាញ់' },
};

export const AUSPICIOUS_DIRECTIONS = [
  { nameKm: 'ទិសឦសាន (North-East)', code: 'NE 45°', note: 'ទិសនាំជោគសំណាង និងលាភសក្ការៈ' },
  { nameKm: 'ទិសបូព៌ា (East)', code: 'E 90°', note: 'ទិសរាសីឡើងខ្ពស់ និងការងារជោគជ័យ' },
  { nameKm: 'ទិសអាគ្នេយ៍ (South-East)', code: 'SE 135°', note: 'ទិសសិរីមង្គល និងភាពចម្រុងចម្រើន' },
  { nameKm: 'ទិសទក្សិណ (South)', code: 'S 180°', note: 'ទិសពង្រឹងកម្លាំងចិត្ត និងតេជះបារមី' },
  { nameKm: 'ទិសនិរតី (South-West)', code: 'SW 225°', note: 'ទិសមេត្តា សេចក្តីស្រឡាញ់' },
  { nameKm: 'ទិសបស្ចិម (West)', code: 'W 270°', note: 'ទិសសន្តិភាព និងភាពត្រជាក់ត្រជុំ' },
  { nameKm: 'ទិសពាយព្យ (North-West)', code: 'NW 315°', note: 'ទិសជំនួយឧបត្ថម្ភ និងការគាំទ្រ' },
];

export const getDailyFengShui = (animalYearName, dayOfWeek) => {
  const conflict = ANIMAL_CONFLICT_MAP[animalYearName] || { conflictName: 'ជូត', emoji: '🐀', note: 'ឆុងឆ្នាំ ជូត' };
  const direction = AUSPICIOUS_DIRECTIONS[dayOfWeek % 7] || AUSPICIOUS_DIRECTIONS[0];
  return {
    conflictAnimal: conflict.conflictName,
    conflictEmoji: conflict.emoji,
    conflictNote: conflict.note,
    directionName: direction.nameKm,
    directionCode: direction.code,
    directionNote: direction.note,
  };
};

/**
 * Western Zodiac Sign calculation
 */
export const getWesternZodiac = (solarMonth, solarDay) => {
  if ((solarMonth === 3 && solarDay >= 21) || (solarMonth === 4 && solarDay <= 19)) {
    return { nameKm: 'មេសរាសី', nameEn: 'Aries', symbol: '♈', note: 'ភាពក្លាហាន និងភាពស្វាហាប់' };
  } else if ((solarMonth === 4 && solarDay >= 20) || (solarMonth === 5 && solarDay <= 20)) {
    return { nameKm: 'ឧសភរាសី', nameEn: 'Taurus', symbol: '♉', note: 'ភាពអត់ធ្មត់ និងស្ថិរភាព' };
  } else if ((solarMonth === 5 && solarDay >= 21) || (solarMonth === 6 && solarDay <= 20)) {
    return { nameKm: 'មេថុនរាសី', nameEn: 'Gemini', symbol: '♊', note: 'ភាពឆ្លាតវៃ និងភាពរួសរាយ' };
  } else if ((solarMonth === 6 && solarDay >= 21) || (solarMonth === 7 && solarDay <= 22)) {
    return { nameKm: 'កក្កដរាសី', nameEn: 'Cancer', symbol: '♋', note: 'សន្តានចិត្ត និងការយកចិត្តទុកដាក់' };
  } else if ((solarMonth === 7 && solarDay >= 23) || (solarMonth === 8 && solarDay <= 22)) {
    return { nameKm: 'សីហរាសី', nameEn: 'Leo', symbol: '♌', note: 'ភាពជាអ្នកដឹកនាំ និងភាពថ្លៃថ្នូរ' };
  } else if ((solarMonth === 8 && solarDay >= 23) || (solarMonth === 9 && solarDay <= 22)) {
    return { nameKm: 'កញ្ញារាសី', nameEn: 'Virgo', symbol: '♍', note: 'ភាពផ្ចិតផ្ចង់ និងចំណេះដឹង' };
  } else if ((solarMonth === 9 && solarDay >= 23) || (solarMonth === 10 && solarDay <= 22)) {
    return { nameKm: 'តុលារាសី', nameEn: 'Libra', symbol: '♎', note: 'តុល្យភាព និងយុត្តិធម៌' };
  } else if ((solarMonth === 10 && solarDay >= 23) || (solarMonth === 11 && solarDay <= 21)) {
    return { nameKm: 'វិច្ឆិករាសី ឬ ព្យាឃ្រ', nameEn: 'Scorpio', symbol: '♏', note: 'ភាពមុតមាំ និងការតស៊ូ' };
  } else if ((solarMonth === 11 && solarDay >= 22) || (solarMonth === 12 && solarDay <= 21)) {
    return { nameKm: 'ធ្នូរាសី', nameEn: 'Sagittarius', symbol: '♐', note: 'ភាពស្មោះត្រង់ និងទស្សនវិស័យវែងឆ្ងាយ' };
  } else if ((solarMonth === 12 && solarDay >= 22) || (solarMonth === 1 && solarDay <= 19)) {
    return { nameKm: 'មកររាសី', nameEn: 'Capricorn', symbol: '♑', note: 'វិន័យ និងការខិតខំប្រឹងប្រែង' };
  } else if ((solarMonth === 1 && solarDay >= 20) || (solarMonth === 2 && solarDay <= 18)) {
    return { nameKm: 'កុម្ភរាសី', nameEn: 'Aquarius', symbol: '♒', note: 'គំនិតច្នៃប្រឌិត និងការស្រឡាញ់មនុស្សជាតិ' };
  } else {
    return { nameKm: 'មីនរាសី', nameEn: 'Pisces', symbol: '♓', note: 'ក្តីមេត្តា និងការច្នៃប្រឌិតខ្ពស់' };
  }
};

/**
 * Get Lunar Month Range spanning across a specific solar Gregorian month
 * E.g., for November 2026: "អស្សុជ - កត្តិក ឆ្នាំមមី"
 */
export const getSolarMonthLunarSpan = (year, monthZeroIndexed) => {
  const firstDay = new Date(year, monthZeroIndexed, 1);
  const lastDay = new Date(year, monthZeroIndexed + 1, 0);

  const startLunar = fromDate(firstDay).khmer;
  const endLunar = fromDate(lastDay).khmer;

  let spanMonth = startLunar.monthName;
  if (startLunar.monthName !== endLunar.monthName) {
    spanMonth = `${startLunar.monthName} - ${endLunar.monthName}`;
  }

  return {
    lunarMonthsText: spanMonth,
    animalYear: endLunar.animalYearName,
    animalEmoji: ANIMAL_EMOJIS[endLunar.animalYearName] || '🐎',
    sakName: endLunar.sakName,
    beYear: toKhmerNumber(endLunar.beYear),
    headerText: `${spanMonth} ឆ្នាំ${endLunar.animalYearName}`
  };
};
