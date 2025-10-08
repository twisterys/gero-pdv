// Number helpers for consistent decimal handling across the app
// Uses window.__decimal_length when available (provided from backend/global config)
// Defaults to 2 decimals and formats with fr-FR locale style (comma decimal, space thousands)

function getDecimalLength() {
  const dl = (typeof window !== 'undefined' && window.__decimal_length != null)
    ? parseInt(window.__decimal_length, 10)
    : 2;
  return Number.isFinite(dl) && dl >= 0 ? dl : 2;
}

function roundNumber(num) {
  const decimals = getDecimalLength();
  const n = Number(num) || 0;
  // Use toFixed to avoid floating errors then convert back to Number
  return Number(n.toFixed(decimals));
}

function formatDecimal(num) {
  const decimals = getDecimalLength();
    console.log(decimals)
    const n = Number(num) || 0;
  // Format with French style: comma as decimal separator and space for thousands
  // Intl may use non-breaking spaces depending on environment; ensure regular spaces
  const formatted = n.toLocaleString('fr-FR', {
    minimumFractionDigits: decimals,
    maximumFractionDigits: decimals,
  });
  return formatted.replace(/\u00A0/g, ' ');
}

export { getDecimalLength, roundNumber, formatDecimal };
