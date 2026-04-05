// CommandController.java (Spring Boot)
import org.springframework.web.bind.annotation.*;
import org.springframework.http.*;
import java.io.*; import java.util.*;

@RestController
@RequestMapping("/cmd")
public class CommandController {

    // Fixed allowlist — keys are user-facing names, values are exact arg lists
    private static final Map<String, List<String>> ALLOWED = Map.of(
        "uptime", List.of("uptime"),
        "disk",   List.of("df", "-h"),
        "date",   List.of("date")
    );

    @GetMapping
    public ResponseEntity<String> run(@RequestParam String name) {
        List<String> cmd = ALLOWED.get(name);

        if (cmd == null) {
            return ResponseEntity.badRequest()
                .body("Unknown command. Allowed: " + ALLOWED.keySet());
        }

        try {
            // ProcessBuilder with List — no shell, no injection surface
            ProcessBuilder pb = new ProcessBuilder(cmd)
                .redirectErrorStream(true);
            Process proc = pb.start();

            String output = new String(
                proc.getInputStream().readAllBytes());
            int exit = proc.waitFor();

            return exit == 0
                ? ResponseEntity.ok(output)
                : ResponseEntity.status(500).body("Command failed.");

        } catch (Exception e) {
            return ResponseEntity.status(500).body("Execution error.");
        }
    }
}
