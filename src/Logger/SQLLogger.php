<?php


namespace ApiMessageDispatcher\Logger;


use Exception;

class SQLLogger extends AbstractLogger implements \Doctrine\DBAL\Logging\SQLLogger
{

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function startQuery($sql, ?array $params = null, ?array $types = null)
    {
        $this->info("Trying to execute the query " . $this->buildQuery($sql, $params, $types));
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function stopQuery()
    {
        $this->info("The previous query has been successfully executed");
    }

    /**
     * @param string $query
     * @param array|null $parameters
     * @param array|null $types
     * @return string
     */
    private function buildQuery(string $query, ?array $parameters, ?array $types): string
    {
        if (!is_null($parameters)) {
            while (count($parameters) > 0) {
                $type = array_shift($types);
                $replacement = array_shift($parameters);
                if ($type == "string") {
                    $replacement = "'" . $replacement . "'";
                }
                if (is_array($replacement)) {
                    $replacement = implode(", ", $replacement);
                }
                $query = preg_replace("/\?/", $replacement, $query, 1);
            }
        }
        return $query;
    }

}