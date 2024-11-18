<?php


?>
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
                <p>Parameters</p>
                <pre><?php echo json_encode($requestData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)?></pre>
                <p>Built Cli</p>
                <pre><?=$builtCli?></pre>
                <p>Built Object</p>
                <pre><?=$builtObject?></pre>
                <p>Parsed Cli Data</p>
                <pre><?php echo json_encode($parsedCli)?></pre>
            </td>
            <td>
                <pre><?=$verboseData?></pre>
            </td>
            <td>
                <pre><?php print_r($responseInfo)?></pre>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <p>Response</p>
                <pre><?php echo json_encode($response)?></pre>
            </td>
        </tr>
    </tbody>
</table>
<style>
    td {
        width: 33.33%;
    }
    pre {
        white-space: pre-wrap; 
        white-space: -moz-pre-wrap;
        word-wrap: break-word;
    }
</style>