@RestController
public class AdminController {
    @PostMapping("/api/tasks")
    public ResponseEntity<String> runTask(@RequestBody TaskRequest task) {
        // Map the request 'name' to a specific hardcoded script
        if ("clean_temp".equals(task.getName())) {
            // Logic to clean temp files safely
            return ResponseEntity.ok("Task started.");
        }
        return ResponseEntity.status(403).body("Invalid Task");
    }
}
