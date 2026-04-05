// HttpCommandServlet.java
import jakarta.servlet.http.*;
import java.io.*; import java.util.*;
import java.util.concurrent.TimeUnit;

@WebServlet("/run")
public class HttpCommandServlet extends HttpServlet {

    // Immutable allowlist: name → exact arg array, no user-controlled tokens
    private static final Map<String, List<String>> ALLOWED = Map.of(
        "date",   List.of("date"),
        "uptime", List.of("uptime"),
        "disk",   List.of("df", "-h", "/")
    );

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse res)
            throws IOException {

        String name = req.getParameter("cmd");
        List<String> cmd = ALLOWED.get(name);

        if (cmd == null) {
            res.sendError(400, "Unknown command: " + name);
            return;
        }

        try {
            Process p = new ProcessBuilder(cmd)
                .redirectErrorStream(true)
                .start();

            boolean done = p.waitFor(5, TimeUnit.SECONDS);
            if (!done) { p.destroyForcibly(); res.sendError(504, "Timeout."); return; }

            String output = new String(p.getInputStream().readAllBytes());
            res.setContentType("text/plain;charset=UTF-8");
            res.getWriter().write(output);

        } catch (Exception e) {
            res.sendError(500, "Execution failed.");
        }
    }
}
