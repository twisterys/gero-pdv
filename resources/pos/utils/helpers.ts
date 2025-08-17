import type {RefObject} from "react";

/**
 * Generates and prints a formatted report from the contents of a provided table reference.
 *
 * This function extracts the HTML content of a specified table referenced via a RefObject, creates a
 * dedicated new print window, and applies styled formatting. Auto-print functionality is included, and
 * the window closes after printing. It also includes error handling for scenarios such as disabled pop-ups
 * or missing content.
 *
 * @param {RefObject<HTMLDivElement|null>} tableRef - A reference to the HTML `<div>` element containing
 *     the table content to print. If the reference is null or does not contain valid content, the print
 *     operation will abort.
 * @param {string} title - The title of the report, which will be displayed as part of the header in the print preview.
 */
export const printReport = (tableRef : RefObject<HTMLDivElement|null> , title:string) => {
    try {
        const printContents = tableRef?.current?.innerHTML;
        if (!printContents) return;
        const currentDate = new Date();

        const printWindow = window.open('', '_blank', 'width=800,height=600');

        if (!printWindow) {
            window.alert('Veuillez autoriser les pop-ups pour l\'impression');
            return;
        }

        // Enhanced print styles
        printWindow.document.write(`
      <!DOCTYPE html>
      <html lang="fr">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>${title}</title>
 <style>
              @page {
                size: A4;
                margin: 2cm 1cm;
              }

              body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
                color: #333;
                line-height: 1.4;
              }

              .print-header {
                position: running(header);
                text-align: center;
                padding-bottom: 10px;
                border-bottom: 1px solid #eee;
              }

              @page {
                @top-center {
                  content: element(header);
                }
              }

              .print-header h1 {
                font-size: 18px;
                margin: 0 0 5px 0;
                color: #2c3e50;
              }

              .print-header .print-date {
                font-size: 12px;
                color: #666;
                margin-bottom: 10px;
              }

              table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
                font-size: 12px;
              }

              th, td {
                border: 1px solid #ddd;
                padding: 6px;
                text-align: center;
              }

              th {
                background-color: #f2f2f2;
                font-weight: bold;
              }

              tr:nth-child(even) {
                background-color: #f9f9f9;
              }

              .no-print {
                display: none !important;
              }

              .page-break {
                page-break-after: always;
              }

              .cell-content {
                page-break-inside: avoid;
              }

              @media print {
                body {
                  padding: 0;
                }

                table {
                  page-break-inside: auto;
                }

                tr {
                  page-break-inside: avoid;
                  page-break-after: auto;
                }
              }
            </style>
        </head>
        <body>
          <div class="print-header">
            <h1>${title}</h1>
            <div class="print-date">
              Généré le: ${currentDate.toLocaleDateString('fr-FR')} à ${currentDate.toLocaleTimeString('fr-FR')}
            </div>
          </div>
          ${printContents}
          <script>
            // Auto-print and close when loaded
            window.onload = function() {
              setTimeout(function() {
                window.print();
                window.close();
              }, 100);
            };
          </script>
        </body>
      </html>
    `);

      print(printWindow);

    } catch (error) {
        console.error('Error during printing:', error);
        window.alert('Une erreur est survenue lors de l\'impression');
    }
};

/**
 * Opens a new browser window or tab to display a printable version of the provided HTML content.
 * The function can handle both full HTML documents and HTML snippets.
 * It attempts to format and print the content with a predefined A4 page style and header including current date and time.
 * If pop-up windows are blocked, the function will alert the user.
 *
 * @param {string} html - The HTML content to be printed. Can be a full document or a snippet.
 * @param {string} [title='Impression'] - The title to display in the new window/tab and in the printed header.
 */
export const printHtml = (html: string, title: string = 'Impression') => {
    try {
        if (!html) return;
        const currentDate = new Date();

        const printWindow = window.open('', '_blank', 'width=800,height=600');
        if (!printWindow) {
            window.alert('Veuillez autoriser les pop-ups pour l\'impression');
            return;
        }

        const isFullDocument = /<html[\s>]|<head[\s>]|<body[\s>]/i.test(html);

        if (isFullDocument) {
            // Assume html contains a complete document
            printWindow.document.write(html);
        } else {
            // Wrap the provided HTML snippet with the same print template/styles
            printWindow.document.write(`
      <!DOCTYPE html>
      <html lang="fr">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>${title}</title>
          <style>
            @page { size: A4; margin: 2cm 1cm; }
            body { font-family: Arial, sans-serif; margin: 0; padding: 20px; color: #333; line-height: 1.4; }
            .print-header { position: running(header); text-align: center; padding-bottom: 10px; border-bottom: 1px solid #eee; }
            @page { @top-center { content: element(header); } }
            .print-header h1 { font-size: 18px; margin: 0 0 5px 0; color: #2c3e50; }
            .print-header .print-date { font-size: 12px; color: #666; margin-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
            th, td { border: 1px solid #ddd; padding: 6px; text-align: center; }
            th { background-color: #f2f2f2; font-weight: bold; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
            .cell-content { page-break-inside: avoid; }
            @media print {
              body { padding: 0; }
              table { page-break-inside: auto; }
              tr { page-break-inside: avoid; page-break-after: auto; }
            }
          </style>
        </head>
        <body>
          <div class="print-header">
            <h1>${title}</h1>
            <div class="print-date">
              Généré le: ${currentDate.toLocaleDateString('fr-FR')} à ${currentDate.toLocaleTimeString('fr-FR')}
            </div>
          </div>
          ${html}
           <script>
            // Auto-print and close when loaded
            window.onload = function() {
              setTimeout(function() {
                window.print();
                window.close();
              }, 100);
            };
          </script>
        </body>
      </html>
    `);
        }
        print(printWindow)

    } catch (error) {
        console.error('Error during printing (HTML):', error);
        window.alert('Une erreur est survenue lors de l\'impression');
    }
};


/**
 * A function that initializes a barcode detection mechanism by attaching a keydown event listener.
 * It detects key sequences that are entered rapidly (simulating barcode scanner input) and focuses
 * the provided input element when the scanner prefix key is detected.
 *
 * @param {RefObject<HTMLInputElement|null>} inputRef - A React ref object pointing to an input element
 *                                                     where focus will be set if the scanner prefix key is detected.
 * @returns {Function} A cleanup function that removes the registered keydown event listener.
 */
export const barcodeDetection = (inputRef:RefObject<HTMLInputElement|null>) :()=> void => {
    const SCANNER_PREFIX_KEY = 'F9';
    let buffer = '';
    let lastKeyTime = Date.now();

    const handleKeyDown = (e: KeyboardEvent) => {
        const now = Date.now();

        // Prefix detection
        if (e.key === SCANNER_PREFIX_KEY) {
            inputRef.current?.focus();
            buffer = '';
            return;
        }

        // Fast typing detection
        const timeDiff = now - lastKeyTime;
        lastKeyTime = now;

        if (timeDiff > 100) {
            buffer = '';
        }

        buffer += e.key;
    };

    document.addEventListener('keydown', handleKeyDown);
    return () => {
        document.removeEventListener('keydown', handleKeyDown);
    };
}


/**
 * Handles printing of content in a specified window. Supports auto-print for complete documents
 * and document snippets. Ensures appropriate cleanup after printing.
 *
 * @param {Window|null} printWindow - The browser window containing the content to be printed.
 *                                    If null, the function exits without any action.
 */
const print = (printWindow:Window|null) => {
    if (!printWindow) return;
    printWindow.document.close();

    // Ensure auto-print for both full-document and snippet cases
    const triggerPrint = () => {
        try {
            printWindow.focus();
            printWindow.print();
        } catch (_) {}
    };

    if (printWindow.document.readyState === 'complete') {
        setTimeout(triggerPrint, 100);
    } else {
        const onLoad = () => {
            setTimeout(triggerPrint, 100);
            printWindow.removeEventListener('load', onLoad);
        };
        printWindow.addEventListener('load', onLoad);
    }

    // Fallback in case the window doesn't close automatically
    printWindow.addEventListener('afterprint', () => {
        setTimeout(() => {
            printWindow.close();
        }, 500);
    });
}

