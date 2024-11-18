<?php


?>
<div class="debug-container">
    <table>
        <thead>
            <tr>
                <th>RequestData</th>
                <th>VerboseData</th>
                <th>ResponseData</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <p>▶ Parameters</p>
                    <pre><?php echo json_encode($requestData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)?></pre>
                    <p>▶ Built Cli</p>
                    <pre><?=$builtCli?></pre>
                    <p>▶ Built Object</p>
                    <pre><?=$builtObject?></pre>
                    <p>▶ Parsed Cli Data</p>
                    <pre><?php echo json_encode($parsedCli, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)?></pre>
                </td>
                <td>
                    <pre><?=$verboseData?></pre>
                </td>
                <td>
                    <pre><?php echo json_encode($responseInfo, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)?></pre>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <p>▶ Response</p>
                    <pre><?php echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)?></pre>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<style>
    .debug-container {
        width: 94vw;
        display: inline-block;
        margin: 3vw;
    }
    .debug-container table {
        margin: 15px;
        border-spacing: 0px;
        border-collapse: separate;
        border: 1px solid white;
    }
    .debug-container td, .debug-container th {
        width: 33.33% !important;
        border: 1px solid white;
        padding: 0px;
        vertical-align: top;
    }
    .debug-container pre, .debug-container p {
        max-width: 100%;
        white-space: pre-wrap; 
        white-space: -moz-pre-wrap;
        padding: 15px;
        word-wrap: break-word;
        background-color: #002800;
        color: #cccccc;
    }
    .debug-container p {
        margin-bottom: 0px;
    }
    .debug-container pre {
        margin-top: 0px;
    }
    .debug-container th {
        padding: 15px;
        font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        font-size: 20px;
    }
</style>