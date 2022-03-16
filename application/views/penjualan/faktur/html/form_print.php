
<div style="text-align:center">
    <h1>Print Raw Commands from Javascript</h1>
    <hr />
    <label class="checkbox">
        <input type="checkbox" id="useDefaultPrinter" /> <strong>Print to Default printer</strong>
    </label>
    <p>or...</p>
    <div id="installedPrinters">
        <label for="installedPrinterName">Select an installed Printer:</label>
        <select name="installedPrinterName" id="installedPrinterName"></select>
    </div>
    <br /><br />
    <button type="button" onclick="print();">Print Now...</button>
</div>