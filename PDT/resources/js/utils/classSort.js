/**
 * Natural Sorting function for School Classes (e.g. 7A..7H, 8A..8H, 9A..9H, 10A..10H, 11A..11H, 12A..12H)
 * Supports string arrays (e.g. ['7A', '10H']) and object arrays (e.g. [{ name: '7A' }]).
 */
export function sortClassesNaturally(list) {
  if (!Array.isArray(list)) return [];
  return [...list].sort((a, b) => {
    const nameA = String(typeof a === 'string' ? a : (a?.name || a?.class_name || '')).trim();
    const nameB = String(typeof b === 'string' ? b : (b?.name || b?.class_name || '')).trim();

    const matchA = nameA.match(/^(\d+)\s*([a-zA-Z]*)/);
    const matchB = nameB.match(/^(\d+)\s*([a-zA-Z]*)/);

    const gradeA = matchA ? parseInt(matchA[1], 10) : 999;
    const gradeB = matchB ? parseInt(matchB[1], 10) : 999;

    if (gradeA !== gradeB) {
      return gradeA - gradeB;
    }

    const secA = matchA ? (matchA[2] || '').toUpperCase() : '';
    const secB = matchB ? (matchB[2] || '').toUpperCase() : '';
    return secA.localeCompare(secB);
  });
}

