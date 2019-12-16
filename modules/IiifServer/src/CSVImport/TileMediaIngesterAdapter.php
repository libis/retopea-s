<?php
namespace IiifServer\CSVImport;

use CSVImport\MediaIngesterAdapter\MediaIngesterAdapterInterface;

class TileMediaIngesterAdapter implements MediaIngesterAdapterInterface
{
    public function getJson($mediaDatum)
    {
        $mediaJson['ingest_filename'] = $mediaDatum;
        return $mediaJson;
    }
}
