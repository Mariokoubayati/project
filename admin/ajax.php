<?php
require_once ("includes/global.php");
header('Content-Type: application/json');

// Parse JSON input
$input = json_decode(file_get_contents('php://input'), true);

$function_name = $input['action'] ?? '';

switch ($function_name){

    case "get_positions_by_department":{

        $department_id = $input['department_id'] ?? null;

         try {
            $stmt = $conn->prepare("SELECT * FROM position_management WHERE department_id = :department_id");
            $stmt->bindParam(":department_id", $department_id, PDO::PARAM_INT);
            $stmt->execute();
            $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode([
                'success' => true,
                'data' => $positions,
            ]);
            return true;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Query failed',
                'details' => $e->getMessage(),
            ]);
        }

        exit;

    }break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid function']);
    exit;
}
