// DynamicClassLoader.java
import java.util.Map; import java.util.function.Supplier;

public class DynamicClassLoader {

    // ✔ Closed registry — user picks a key, not a class name
    private static final Map<String, Class<? extends Runnable>> REGISTRY =
        Map.of(
            "reportJob",  com.myapp.jobs.ReportJob.class,
            "cleanupJob", com.myapp.jobs.CleanupJob.class,
            "syncJob",    com.myapp.jobs.SyncJob.class
        );

    // ❌ INSECURE — never do this
    public static Object loadUnsafe(String className) throws Exception {
        return Class.forName(className).getDeclaredConstructor().newInstance();
        // ❌ forName("java.lang.Runtime") + reflection → arbitrary execution
    }

    // ✔ SECURE — registry lookup; user supplies a key, not a class name
    public static Runnable loadSafe(String jobName) throws Exception {
        Class<? extends Runnable> clazz = REGISTRY.get(jobName);

        if (clazz == null) {
            throw new IllegalArgumentException(
                "Unknown job: '" + jobName + "'. Allowed: " + REGISTRY.keySet());
        }

        // Instantiate only the pre-approved class, not whatever the user typed
        return clazz.getDeclaredConstructor().newInstance();
    }

    public static void main(String[] args) throws Exception {
        String job = args.length > 0 ? args[0] : "reportJob";
        Runnable task = loadSafe(job);
        task.run();
    }
}
