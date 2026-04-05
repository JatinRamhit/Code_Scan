// RedirectServlet.java
import jakarta.servlet.http.*;
import java.io.*; import java.net.*; import java.util.Set;

@WebServlet("/redirect")
public class RedirectServlet extends HttpServlet {

    // Explicit allowlist of destinations — never redirect to arbitrary URLs
    private static final Set<String> ALLOWED_HOSTS =
        Set.of("myapp.com", "www.myapp.com", "api.myapp.com");

    @Override
    protected void doGet(HttpServletRequest req, HttpServletResponse res)
            throws IOException {

        String target = req.getParameter("url");

        if (target == null || target.isBlank()) {
            res.sendRedirect("/home");  // safe default
            return;
        }

        try {
            URI uri = new URI(target).normalize();

            // Block non-HTTP schemes (javascript:, data:, file:, etc.)
            String scheme = uri.getScheme();
            if (scheme != null && !scheme.equalsIgnoreCase("https")
                               && !scheme.equalsIgnoreCase("http")) {
                res.sendError(400, "Scheme not permitted.");
                return;
            }

            String host = uri.getHost();
            if (host != null && !ALLOWED_HOSTS.contains(host.toLowerCase())) {
                res.sendError(403, "Redirect to this host is not allowed.");
                return;
            }

            res.sendRedirect(uri.toString());

        } catch (URISyntaxException e) {
            res.sendError(400, "Invalid URL.");
        }
    }
}
