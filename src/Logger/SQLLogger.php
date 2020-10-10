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
        while(count($parameters) > 0) {
            var_dump($parameters[0]);
            var_dump($types[0]);
            $replacement = $parameters[0];
            if ($types[0] == "string") {
                $replacement = "'" . $parameters[0] . "'";
            }
            if (is_array($replacement)) {
                $replacement = implode(", ", $replacement);
            }
            $query = preg_replace("/\?/", $replacement, $query, 1);
            unset($parameters[0]);
            unset($types[0]);
            $parameters = array_values($parameters);
            $types = array_values($types);
        }
        return $query;
    }

}