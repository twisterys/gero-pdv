import { useEffect, useMemo } from "react";
import { useRapportsStore } from "../../stores/rapports-store";

export default function DepensesPage() {
  const { isLoading, depenses, getDepenses, error, isError } = useRapportsStore();

  useEffect(() => {
    getDepenses();
  }, [getDepenses]);

  const total = useMemo(() => {
    try {
      return (depenses || []).reduce((sum: number, row: any) => sum + (Number(row?.montant) || 0), 0);
    } catch {
      return 0;
    }
  }, [depenses]);

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h1 className="text-xl font-semibold">Rapport de Dépenses</h1>
        <div className="space-x-2">
          <button
            className="btn btn-primary"
            disabled={isLoading}
            onClick={() => getDepenses()}
          >
            Actualiser
          </button>
        </div>
      </div>

      {isError && (
        <div className="p-3 bg-red-50 text-red-700 rounded border border-red-200">
          {error || "Erreur lors du chargement du rapport des dépenses."}
        </div>
      )}

      <div className="rounded border border-gray-200 overflow-hidden bg-white">
        <div className="overflow-x-auto">
          <table className="min-w-full table-auto">
            <thead>
              <tr className="bg-gray-50">
                <th className="px-3 py-2 text-left text-sm font-medium text-gray-600">Catégorie</th>
                <th className="px-3 py-2 text-right text-sm font-medium text-gray-600">Montant</th>
              </tr>
            </thead>
            <tbody>
              {(depenses && depenses.length > 0) ? (
                depenses.map((row: any, idx: number) => (
                  <tr key={idx} className="border-t border-gray-100">
                    <td className="px-3 py-2 text-sm text-gray-800">{row?.categorie ?? '-'}</td>
                    <td className="px-3 py-2 text-right text-sm tabular-nums">
                      {new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(row?.montant) || 0)}
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={2} className="px-3 py-6 text-center text-sm text-gray-500">
                    {isLoading ? 'Chargement…' : 'Aucune dépense trouvée'}
                  </td>
                </tr>
              )}
            </tbody>
            <tfoot>
              <tr className="bg-gray-50 border-t border-gray-200">
                <td className="px-3 py-2 text-right font-medium">Total</td>
                <td className="px-3 py-2 text-right font-semibold tabular-nums">
                  {new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(total)}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  );
}
